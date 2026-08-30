<?php

namespace App\Http\Controllers\Api\Mobile\Student;

use App\Enums\CourseStatus;
use App\Enums\Level;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SetApiLocale;
use App\Models\Category;
use App\Models\Courses\Course;
use App\Models\Courses\CourseSection;
use App\Models\Courses\Enrollment;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public course browsing endpoints for the student mobile app.
 *
 * Enrollment-check runs per-request when a user is authenticated,
 * so the `is_enrolled` field is always accurate.
 *
 * Content is language-scoped exactly like the Blade site
 * (`CourseController::index()` → `->where('lang', $lang)`): courses are stored
 * one row per language, so the app only ever sees rows matching the locale
 * resolved by {@see SetApiLocale}. Pass `?lang=all` to opt out.
 */
class MobileStudentCourseController extends Controller
{
    use ApiResponse;

    /** Eager-loads shared by every listing endpoint. */
    private const LIST_RELATIONS = ['tutor:id,name', 'category:id,name,name_ar,slug', 'activePrice'];

    // ── Browse / Index ────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category'    => ['nullable', 'string', 'max:100'],
            'level'       => ['nullable', 'string', 'in:beginner,intermediate,advanced,preview'],
            'is_free'     => ['nullable', 'boolean'],
            'search'      => ['nullable', 'string', 'max:100'],
            'per_page'    => ['nullable', 'integer', 'min:5', 'max:50'],
            'sort_by'     => ['nullable', 'string', 'in:popular,rating,price_asc,price_desc,newest'],
            'lang'        => ['nullable', 'string', 'max:5'],
        ]);

        $query = $this->publishedCourses($request)
            ->with(self::LIST_RELATIONS)
            ->withCount(['students', 'lessons'])
            ->sortByOption($request->sort_by)
            ->searchCourse($request->search);

        // Filters
        if ($categoryId = $this->resolveCategoryId($request)) {
            $query->where('category_id', $categoryId);
        }

        if ($level = $this->resolveLevel($request->input('level'))) {
            $query->where('level', $level);
        }

        if ($request->has('is_free')) {
            $query->where('is_free', $request->boolean('is_free'));
        }

        $perPage = $request->integer('per_page', 15);
        $courses = $query->paginate($perPage);

        return $this->paginated($courses, fn ($items) => $this->formatCourseList($items, $request));
    }

    // ── Featured ──────────────────────────────────────────────────────────────

    /**
     * "Featured" is a derived concept — the `courses` table has no
     * `is_featured` flag — so the most-enrolled published courses of the
     * current language are surfaced (highest rated first on ties).
     */
    public function featured(Request $request): JsonResponse
    {
        $courses = $this->publishedCourses($request)
            ->with(self::LIST_RELATIONS)
            ->withCount(['students', 'lessons'])
            ->withAvg('ratings', 'rating')
            ->orderByDesc('students_count')
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return $this->success($this->formatCourseList($courses->all(), $request));
    }

    // ── Search ────────────────────────────────────────────────────────────────

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'sort_by' => ['nullable', 'string', 'in:popular,rating,price_asc,price_desc,newest'],
            'lang' => ['nullable', 'string', 'max:5'],
        ]);

        $term    = $request->q;
        $courses = $this->publishedCourses($request)
            ->searchCourse($term)
            ->sortByOption($request->sort_by)
            ->with(self::LIST_RELATIONS)
            ->withCount(['students', 'lessons'])
            ->limit(20)
            ->get();

        return $this->success($this->formatCourseList($courses->all(), $request));
    }

    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'lang' => ['nullable', 'string', 'max:5'],
        ]);

        $term = $request->q;
        $courses = $this->publishedCourses($request)
            ->searchCourse($term)
            ->with('category:id,name,name_ar,slug')
            ->limit(5)
            ->get(['id', 'slug', 'title', 'category_id']);

        return $this->success($courses->map(fn ($c) => [
            'slug' => $c->slug,
            'title' => $c->title,
            'category' => $c->category?->localized_name,
        ]));
    }

    // ── Recommended ───────────────────────────────────────────────────────────

    public function recommended(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        $query = $this->publishedCourses($request)
            ->with(self::LIST_RELATIONS)
            ->withCount(['students', 'lessons']);

        if ($user) {
            $enrolledCategoryIds = Enrollment::where('user_id', $user->id)
                ->join('courses', 'enrollments.course_id', '=', 'courses.id')
                ->pluck('courses.category_id')
                ->filter()
                ->unique()
                ->toArray();

            if (!empty($enrolledCategoryIds)) {
                $query->whereIn('category_id', $enrolledCategoryIds);
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('students_count', 'desc');
            }
        } else {
            $query->orderBy('students_count', 'desc');
        }

        $courses = $query->limit(10)->get();

        return $this->success($this->formatCourseList($courses->all(), $request));
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Request $request, string $slug): JsonResponse
    {
        $course = Course::query()
            ->where('slug', $slug)
            ->whereIn('status', [CourseStatus::published->value, CourseStatus::preview->value])
            ->with([
                'tutor:id,name,bio',
                'category:id,name,name_ar,slug',
                'activePrice',
                'sections' => fn ($q) => $q->where('status', 'published')
                                            ->orderBy('order')
                                            ->with(['lessons' => fn ($lq) => $lq->orderBy('lesson_order')]),
            ])
            ->withCount(['students', 'lessons'])
            ->firstOrFail();

        $isEnrolled       = false;
        $progressPercent  = 0;
        $enrollment       = null;
        $user             = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();

        if ($user) {
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
            $isEnrolled      = $enrollment !== null;
            $progressPercent = $enrollment?->progress ?? 0;
        }

        return $this->success([
            'id'              => $course->id,
            'slug'            => $course->slug,
            'title'           => $course->title,
            'description'     => $course->description,
            'objectives'      => $course->objectives,
            'cover_image'     => $course->cover_image,
            'level'           => $this->levelKey($course->level),
            'is_free'         => (bool) $course->is_free,
            'price'           => $course->activePrice?->amount ?? 0,
            'duration_minutes'=> $course->duration ?? 0,
            'students_count'  => $course->students_count,
            'lessons_count'   => $course->lessons_count,
            'average_rating'  => (float) $course->average_rating,
            'language'        => $course->lang,
            'is_enrolled'     => $isEnrolled,
            'completion_percent' => $progressPercent,
            'tutor'           => $course->tutor ? [
                'id'     => $course->tutor->id,
                'name'   => $course->tutor->name,
                'bio'    => $course->tutor->bio ?? null,
                'avatar' => $course->tutor->profile,
            ] : null,
            'category'        => $this->formatCategory($course->category),
            'sections'        => $course->sections->map(fn ($section) => [
                'id'      => $section->id,
                'title'   => $section->title,
                'order'   => $section->order,
                'lessons' => $section->lessons->map(fn ($lesson) => [
                    'id'          => $lesson->id,
                    'title'       => $lesson->title,
                    'duration'    => $lesson->duration ?? 0,
                    'is_preview'  => (bool) ($lesson->is_preview ?? false),
                    'is_completed'=> ($isEnrolled && $user)
                        ? $lesson->completed($user->id)
                        : false,
                ]),
            ]),
        ]);
    }

    // ── Categories ────────────────────────────────────────────────────────────

    /**
     * The exact category set the Blade site renders in its "Categories"
     * dropdown (`Category::forLocale($lang)->get()`), plus the `slug` used for
     * deep-linking and a locale-scoped published-course count.
     *
     * `name` is already localised for the resolved locale, so the client can
     * render it verbatim; `name_en` / `name_ar` are kept for completeness.
     */
    public function categories(Request $request): JsonResponse
    {
        $locale = $this->locale();
        $lang   = $this->langFilter($request);

        $categories = Category::query()
            ->forLocale($locale)
            ->withCount([
                'courses as courses_count' => function ($q) use ($lang) {
                    $q->where('status', CourseStatus::published->value);
                    if ($lang) {
                        $q->where('lang', $lang);
                    }
                },
            ])
            ->get()
            ->map(fn (Category $c) => [
                'id'            => $c->id,
                'name'          => $c->localized_name,
                'name_en'       => $c->name,
                'name_ar'       => $c->name_ar ?: $c->name,
                'slug'          => $c->slug,
                'courses_count' => (int) $c->courses_count,
            ])
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return $this->success($categories);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Locale resolved by the `api_locale` middleware. */
    private function locale(): string
    {
        $locale = app()->getLocale();

        return in_array($locale, SetApiLocale::SUPPORTED, true) ? $locale : 'en';
    }

    /**
     * The `courses.lang` value to scope to, or `null` for "every language".
     *
     * Clients may send `?lang=all` (or `?lang=en|ar`) to override the locale.
     */
    private function langFilter(Request $request): ?string
    {
        $requested = $request->query('lang');

        if (filled($requested)) {
            $requested = strtolower((string) $requested);

            if ($requested === 'all') {
                return null;
            }

            if (in_array($requested, SetApiLocale::SUPPORTED, true)) {
                return $requested;
            }
        }

        return $this->locale();
    }

    /** Published courses, scoped to the requested language. */
    private function publishedCourses(Request $request): Builder
    {
        $query = Course::query()->where('status', CourseStatus::published->value);

        if ($lang = $this->langFilter($request)) {
            $query->where('lang', $lang);
        }

        return $query;
    }

    /** Accepts `category_id`, or a `category` slug / name (website parity). */
    private function resolveCategoryId(Request $request): ?int
    {
        if ($request->filled('category_id')) {
            return (int) $request->input('category_id');
        }

        $category = $request->input('category');

        if (blank($category)) {
            return null;
        }

        return Category::query()
            ->where('slug', $category)
            ->orWhere('name', $category)
            ->orWhere('name_ar', $category)
            ->value('id');
    }

    /**
     * Maps the public level key to the stored column value.
     *
     * `Level::Advanced` is persisted as `'preview'` (legacy), so a literal
     * `where('level', 'advanced')` would never match anything.
     */
    private function resolveLevel(?string $level): ?string
    {
        if (blank($level)) {
            return null;
        }

        return match (strtolower($level)) {
            'beginner'     => Level::Beginner->value,
            'intermediate' => Level::Intermediate->value,
            'advanced', 'preview' => Level::Advanced->value,
            default        => null,
        };
    }

    /** Inverse of {@see resolveLevel()} — stored value → public level key. */
    private function levelKey(Level|string|null $level): string
    {
        $value = $level instanceof Level ? $level->value : (string) $level;

        return match ($value) {
            Level::Advanced->value     => 'advanced',
            Level::Intermediate->value => 'intermediate',
            '', null                   => 'beginner',
            default                    => $value,
        };
    }

    /** @return array{id:int,name:string,name_en:string,name_ar:string,slug:?string}|null */
    private function formatCategory(?Category $category): ?array
    {
        if (! $category) {
            return null;
        }

        return [
            'id'      => $category->id,
            'name'    => $category->localized_name,
            'name_en' => $category->name,
            'name_ar' => $category->name_ar ?: $category->name,
            'slug'    => $category->slug,
        ];
    }

    private function formatCourseList(array $courses, Request $request): array
    {
        $user   = $request->user('sanctum') ?? auth('sanctum')->user() ?? $request->user();
        $userId = $user?->id;

        // Batch-load enrollment data if authenticated
        $enrolledCourseIds = [];
        if ($userId && count($courses) > 0) {
            $courseIds          = array_column($courses, null, 'id');
            $enrolledCourseIds  = Enrollment::where('user_id', $userId)
                ->whereIn('course_id', array_keys($courseIds))
                ->pluck('progress', 'course_id')
                ->toArray();
        }

        return array_map(function ($course) use ($enrolledCourseIds) {
            $courseId = $course instanceof \Illuminate\Database\Eloquent\Model
                ? $course->id
                : $course['id'];

            $course = $course instanceof \Illuminate\Database\Eloquent\Model
                ? $course
                : (object) $course;

            return [
                'id'              => $course->id,
                'slug'            => $course->slug,
                'title'           => $course->title,
                'cover_image'     => $course->cover_image,
                'level'           => $this->levelKey($course->level),
                'is_free'         => (bool) $course->is_free,
                'price'           => $course->activePrice?->amount ?? 0,
                'duration_minutes'=> $course->duration ?? 0,
                'students_count'  => $course->students_count ?? 0,
                'lessons_count'   => $course->lessons_count ?? 0,
                'average_rating'  => (float) ($course->average_rating ?? 0),
                'language'        => $course->lang ?? null,
                'is_enrolled'     => array_key_exists($courseId, $enrolledCourseIds),
                'completion_percent' => $enrolledCourseIds[$courseId] ?? 0,
                'tutor'           => $course->tutor ? [
                    'id'     => $course->tutor->id,
                    'name'   => $course->tutor->name,
                    'avatar' => $course->tutor->profile ?? null,
                ] : null,
                'category'        => $this->formatCategory($course->category),
            ];
        }, $courses);
    }
}
