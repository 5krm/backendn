<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transform-gpu transition-all duration-700 ease-out hover:-translate-y-1 hover:scale-[1.01] hover:shadow-lg will-change-transform group"
    flex flex-col justify-between space-y-4">
    <div>
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ $rating->course->cover_image }}" alt="{{ $rating->course->title }}"
                    class="h-14 w-14 rounded-xl object-cover" />
                <div>
                    <span
                        class="inline-block mt-1 rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">{{ $rating->course->category->name }}</span>
                    <h3 class="mt-2 text-xl font-bold text-gray-900 leading-snug group-hover:text-primary">
                        <a href="{{ route('courses.details', $rating->course) }}">
                            {{ $rating->course->title }}
                        </a>
                    </h3>
                    <p class="text-slate-500 text-sm">{{__('student.course.with')}}
                        <a href="{{ route('tutor.index', $rating->course->tutor) }}" target="_blank"
                            class="mx-1 font-bold link">{{ $rating->course->tutor->name }}</a>
                    </p>

                </div>
            </div>
            <a href="{{ route('app.courses.rate', $rating->course) }}" target="_blank"
                class="text-gray-400 hover:text-gray-600 p-1">
                <i class="icon-[mdi--pencil-outline] size-6"></i>
            </a>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-1 text-amber-400">
                <?php $stars = $rating->rating; ?>
                @for ($i = 1; $i <= $stars; $i++)
                    <i class="icon-[mdi--star] size-5 text-amber-400"></i>
                @endfor
                @for ($i = 1; $i <= 5 - $stars; $i++)
                    <i class="icon-[mdi--star-outline] size-5 text-amber-400"></i>
                @endfor

            </div>
            <span class="text-xs text-gray-400 font-medium">{{ $rating->created_at->format('M d, Y') }}</span>
        </div>


        <div class="flex gap-1 mt-3 text-sm text-slate-500 leading-relaxed px-1 py-2 bg-accent/50 rounded-lg">
            @if ($rating->review)
                <i class="icon-[mdi--format-quote-open-outline] size-4 text-primary me-1">“</i>
                <p>{{ $rating->review }}</p>
            @else
                <p class="text-sm w-full flex items-center gap-1 text-slate-500 justify-center">
                    <i class="icon-[mdi--alert-circle-outline] size-4"></i> {{__('student.no_review_msg')}}
                </p>
            @endif
        </div>
    </div>
</div>
