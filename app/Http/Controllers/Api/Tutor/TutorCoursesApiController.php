<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Enums\CourseStatus;
use App\Enums\Level;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Courses\Course;
use App\Models\Courses\CoursePrice;
use App\Models\Courses\CourseSection;
use App\Models\Lessons\Lesson;
use App\Models\Quizzes\Quiz;
use App\Models\Quizzes\QuizOption;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TutorCoursesApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Course::where('tutor_id', $user->id)
            ->with(['category:id,name', 'activePrice'])
            ->withCount(['students', 'lessons']);

        if ($status === 'published') {
            $query->where('status', CourseStatus::published);
        } elseif ($status === 'draft') {
            $query->where('status', CourseStatus::draft);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $courses = $query->orderByDesc('created_at')->get();

        return $this->success($courses->map(function ($c, $idx) {
            $tones = ['primary', 'secondary', 'accent'];
            $isDraft = $c->status === CourseStatus::draft || $c->status === 'draft';

            return [
                'id' => $c->id,
                'slug' => $c->slug,
                'title' => $c->title,
                'category' => $c->category?->name ?? 'General',
                'level' => $this->levelKey($c->level),
                'students_count' => $c->students_count,
                'lessons_count' => $c->lessons_count,
                'rating' => (float) ($c->average_rating ?? 0.0),
                'status' => $isDraft ? 'draft' : 'published',
                'progress' => $isDraft ? 60 : 100,
                'price' => (float) ($c->activePrice?->price ?? ($c->price ?? 0)),
                'is_free' => (bool) $c->is_free,
                'tone' => $tones[$idx % 3],
                'cover_image' => $c->cover_image,
                'created_at' => $c->created_at?->toISOString(),
            ];
        }));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'learning_objectives' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category' => ['nullable', 'string'],
            'level' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'lang' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $user = $request->user();
        $categoryId = $this->resolveCategory($request, $data);
        $level = $this->resolveLevel($data['level'] ?? null);
        $status = $this->resolveStatus($data['status'] ?? null);
        $objectives = $data['learning_objectives'] ?? $data['objectives'] ?? '';
        $lang = $data['language'] ?? $data['lang'] ?? 'en';
        $oldPrice = $data['original_price'] ?? $data['old_price'] ?? null;
        $price = $data['price'] ?? 0;

        $course = Course::create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']).'-'.Str::random(5),
            'description' => $data['description'] ?? '',
            'objectives' => $objectives,
            'lang' => $lang,
            'category_id' => $categoryId,
            'level' => $level,
            'tutor_id' => $user->id,
            'status' => $status,
            'is_free' => $data['is_free'] ?? false,
            'price' => $price,
            'old_price' => $oldPrice,
        ]);

        if (! empty($price) && empty($data['is_free'])) {
            CoursePrice::create([
                'course_id' => $course->id,
                'price' => $price,
                'is_active' => true,
            ]);
        }

        if ($request->hasFile('cover_image')) {
            $course->addMediaFromRequest('cover_image')->toMediaCollection('cover');
        }

        // Create default initial section
        CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Module 1: Introduction & Foundations',
            'order' => 1,
        ]);

        return $this->created($this->formatCourseDetails($course->fresh()), 'Course created successfully');
    }

    public function show(Request $request, $id): JsonResponse
    {
        $course = Course::with([
            'category',
            'sections.lessons.quizzes.quizOptions',
            'sections.lessons.resources',
            'testimonials',
            'activePrice',
        ])->find($id);

        if (! $course) {
            return $this->notFound('Course not found.');
        }

        return $this->success($this->formatCourseDetails($course));
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        if ($course->tutor_id !== $request->user()->id) {
            return $this->forbidden('You do not own this course.');
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'learning_objectives' => ['nullable', 'string'],
            'objectives' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category' => ['nullable', 'string'],
            'level' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'lang' => ['nullable', 'string'],
        ]);

        if (array_key_exists('level', $data)) {
            $data['level'] = $this->resolveLevel($data['level']);
        }
        if (array_key_exists('status', $data)) {
            $data['status'] = $this->resolveStatus($data['status']);
        }
        if (array_key_exists('learning_objectives', $data)) {
            $data['objectives'] = $data['learning_objectives'];
            unset($data['learning_objectives']);
        }
        if (array_key_exists('language', $data)) {
            $data['lang'] = $data['language'];
            unset($data['language']);
        }
        if (array_key_exists('original_price', $data)) {
            $data['old_price'] = $data['original_price'];
            unset($data['original_price']);
        }
        if (empty($data['category_id']) && ! empty($request->input('category'))) {
            $data['category_id'] = $this->resolveCategory($request, $data);
        }
        unset($data['category']);

        $course->update(array_filter($data, fn ($val) => ! is_null($val)));

        if (! empty($data['price']) && empty($data['is_free'])) {
            CoursePrice::updateOrCreate(
                ['course_id' => $course->id, 'is_active' => true],
                ['price' => $data['price']]
            );
        }

        if ($request->hasFile('cover_image')) {
            $course->clearMediaCollection('cover');
            $course->addMediaFromRequest('cover_image')->toMediaCollection('cover');
        }

        return $this->success($this->formatCourseDetails($course->fresh()), 'Course updated successfully');
    }

    public function publish(Request $request, Course $course): JsonResponse
    {
        if ($course->tutor_id !== $request->user()->id) {
            return $this->forbidden('You do not own this course.');
        }

        $course->update(['status' => CourseStatus::published]);

        return $this->success($this->formatCourseDetails($course->fresh()), 'Course published successfully');
    }

    public function redraft(Request $request, Course $course): JsonResponse
    {
        if ($course->tutor_id !== $request->user()->id) {
            return $this->forbidden('You do not own this course.');
        }

        $course->update(['status' => CourseStatus::draft]);

        return $this->success($this->formatCourseDetails($course->fresh()), 'Course reverted to draft');
    }

    public function destroy(Request $request, Course $course): JsonResponse
    {
        if ($course->tutor_id !== $request->user()->id) {
            return $this->forbidden('You do not own this course.');
        }

        $course->delete();

        return $this->success(null, 'Course deleted successfully');
    }

    // ── Sections & Lessons Management ────────────────────────────────────────

    public function storeSection(Request $request, Course $course): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $nextOrder = CourseSection::where('course_id', $course->id)->max('order') + 1;

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'order' => $nextOrder,
        ]);

        return $this->created($section, 'Section created successfully');
    }

    public function updateSection(Request $request, CourseSection $section): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $section->update($data);

        return $this->success($section, 'Section updated successfully');
    }

    public function deleteSection(Request $request, CourseSection $section): JsonResponse
    {
        $section->lessons()->delete();
        $section->delete();

        return $this->success(null, 'Section deleted successfully');
    }

    public function storeLesson(Request $request, CourseSection $section): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'format' => ['nullable', 'string'], // standard, live_session, practical_assignment
            'duration_minutes' => ['nullable', 'integer'],
            'video_url' => ['nullable', 'string'],
            'meeting_url' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $maxOrder = Lesson::where('section_id', $section->id)->max('order') ?? 0;

        $lesson = Lesson::create([
            'course_id' => $section->course_id,
            'section_id' => $section->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'duration' => $data['duration_minutes'] ?? 15,
            'video_url' => $data['video_url'] ?? null,
            'status' => CourseStatus::published,
            'is_ready' => true,
            'order' => $maxOrder + 1,
        ]);

        return $this->created($lesson, 'Lesson created successfully');
    }

    public function updateLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer'],
            'video_url' => ['nullable', 'string'],
        ]);

        $lesson->update(array_filter($data, fn ($v) => ! is_null($v)));

        return $this->success($lesson, 'Lesson updated successfully');
    }

    public function deleteLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $lesson->delete();

        return $this->success(null, 'Lesson deleted successfully');
    }

    public function storeQuiz(Request $request, Lesson $lesson): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'options' => ['required', 'array', 'min:2'],
            'options.*.text' => ['required', 'string'],
            'options.*.is_correct' => ['required', 'boolean'],
        ]);

        $quiz = Quiz::create([
            'course_id' => $lesson->course_id,
            'lesson_id' => $lesson->id,
            'title' => $data['question'],
            'question' => $data['question'],
        ]);

        foreach ($data['options'] as $idx => $opt) {
            QuizOption::create([
                'quiz_id' => $quiz->id,
                'option' => $opt['text'],
                'is_correct' => $opt['is_correct'],
                'order' => $idx + 1,
            ]);
        }

        return $this->created($quiz->load('quizOptions'), 'Quiz question created successfully');
    }

    // ── Helper formatters ───────────────────────────────────────────────────

    private function resolveCategory(Request $request, array $data): ?int
    {
        if (! empty($data['category_id'])) {
            return (int) $data['category_id'];
        }

        $cat = $request->input('category');
        if (! blank($cat)) {
            return Category::where('id', $cat)
                ->orWhere('slug', $cat)
                ->orWhere('name', $cat)
                ->orWhere('name_ar', $cat)
                ->value('id');
        }

        return null;
    }

    private function resolveLevel(?string $level): Level
    {
        if (blank($level)) {
            return Level::Beginner;
        }

        return match (strtolower($level)) {
            'beginner' => Level::Beginner,
            'intermediate' => Level::Intermediate,
            'advanced', 'preview' => Level::Advanced,
            default => Level::tryFrom(strtolower($level)) ?? Level::Beginner,
        };
    }

    private function levelKey(Level|string|null $level): string
    {
        $value = $level instanceof Level ? $level->value : (string) $level;

        return match ($value) {
            Level::Beginner->value => 'beginner',
            Level::Intermediate->value => 'intermediate',
            Level::Advanced->value => 'advanced',
            default => 'beginner',
        };
    }

    private function resolveStatus(?string $status): CourseStatus
    {
        if (blank($status)) {
            return CourseStatus::draft;
        }

        return CourseStatus::tryFrom(strtolower($status)) ?? CourseStatus::draft;
    }

    private function formatCourseDetails(Course $course): array
    {
        $sections = $course->sections->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'order' => $s->order,
            'lessons' => $s->lessons->map(fn ($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'content' => $l->content,
                'duration_minutes' => $l->duration ?? 15,
                'video_url' => $l->video_url,
                'quizzes' => $l->quizzes->map(fn ($q) => [
                    'id' => $q->id,
                    'question' => $q->question_text,
                    'options' => $q->quizOptions->map(fn ($o) => [
                        'id' => $o->id,
                        'text' => $o->option,
                        'is_correct' => (bool) $o->is_correct,
                    ]),
                ]),
            ]),
        ]);

        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'learning_objectives' => $course->objectives,
            'objectives' => $course->objectives,
            'category' => $course->category?->name ?? 'General',
            'level' => $this->levelKey($course->level),
            'status' => $course->status?->value ?? 'draft',
            'price' => (float) ($course->activePrice?->price ?? ($course->price ?? 0)),
            'original_price' => (float) ($course->old_price ?? 0),
            'is_free' => (bool) $course->is_free,
            'students_count' => $course->students()->count(),
            'average_rating' => (float) ($course->average_rating ?? 0),
            'sections' => $sections,
        ];
    }
}
