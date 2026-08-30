<div >
    <!-- Tabs Header -->
    <div class="border-b border-gray-200 max-sm:overflow-x-scroll overflow-y-hidden ">
        <nav class="-mb-px flex gap-8 text-sm font-medium">

            <button type="button" wire:click="setTab('in-progress')"
                class="inline-flex items-center gap-2 border-b-2 py-4 transition-colors {{ $activeTab === 'in-progress' ? 'border-emerald-500 text-emerald-500 font-semibold' : 'border-transparent text-gray-400 hover:border-gray-300 hover:text-gray-600' }}">
                <i class="icon-[mdi--book-open-blank-variant-outline] size-4"></i>
                <span>{{ __('student.totals.in_progress') }}</span>
            </button>

            <button type="button" wire:click="setTab('completed')"
                class="inline-flex items-center gap-2 border-b-2 py-4 transition-colors {{ $activeTab === 'completed' ? 'border-emerald-500 text-emerald-500 font-semibold' : 'border-transparent text-gray-400 hover:border-gray-300 hover:text-gray-600' }}">
                <i class="icon-[mdi--check-circle-outline] size-4"></i>
                <span>{{ __('student.totals.completed') }}</span>
            </button>

            <button type="button" wire:click="setTab('saved')"
                class="inline-flex items-center gap-2 border-b-2 py-4 transition-colors {{ $activeTab === 'saved' ? 'border-emerald-500 text-emerald-500 font-semibold' : 'border-transparent text-gray-400 hover:border-gray-300 hover:text-gray-600' }}">
                <i class="icon-[mdi--heart-outline] size-4"></i>
                <span>{{ __('student.totals.wishlist') }}</span>
            </button>

            <button type="button" wire:click="setTab('certificates')"
                class="inline-flex items-center gap-2 border-b-2 py-4 transition-colors {{ $activeTab === 'certificates' ? 'border-emerald-500 text-emerald-500 font-semibold' : 'border-transparent text-gray-400 hover:border-gray-300 hover:text-gray-600' }}">
                <i class="icon-[mdi--seal] size-4"></i>
                <span>{{ __('student.totals.certificates') }}</span>
            </button>
            <button type="button" wire:click="setTab('reviews')"
                class="inline-flex items-center gap-2 border-b-2 py-4 transition-colors {{ $activeTab === 'reviews' ? 'border-emerald-500 text-emerald-500 font-semibold' : 'border-transparent text-gray-400 hover:border-gray-300 hover:text-gray-600' }}">
                <i class="icon-[mdi--star-outline] size-4"></i>
                <span>{{ __('student.totals.my_reviews') }}</span>
            </button>

        </nav>
    </div>

    <div class="mt-6">
        @if ($activeTab === 'in-progress')
            <div>
                <p class="mb-3 text-gray-600"><span
                        class="font-bold me-2">{{ __('student.totals.in_progress') }}</span> —
                    {{ __('student.totals.in_progress_caption') }}
                </p>
                <div class="my-3 flex flex-wrap gap-5 space-y-3">
                    @foreach ($enrollments->where('progress', '<', 100) as $enrollment)
                        <? if(!$enrollment->relationLoaded('course')){
                            $enrollment->load(['course' => function ($query) {
                                $query->withCount('lessons');
                            },'course.media', 'course.category', 'course.tutor', 'course.ratings']);
                        }?>
                        <x-my_courses.my-course-card :enrollment="$enrollment" type="in-progress">
                            <a href="{{ route('app.lessons.by-course', $enrollment->course) }}"
                                class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-600 active:scale-[0.98]">
                                <i class="icon-[mdi--play] size-4 "></i>
                                <span>{{ __('student.keep_going') }}</span>
                            </a>
                        </x-my_courses.my-course-card>
                    @endforeach
                    @if ($enrollments->where('progress', '<', 100)->count() == 0)
                        <x-courses.empty-state />
                    @endif
                </div>
            </div>
        @elseif ($activeTab === 'completed')
            <div>
                <p class="text-gray-600"><span class="font-bold me-2">{{ __('student.totals.completed') }}</span> —
                    {{ __('student.totals.completed_caption') }}</p>
                    
                    @if ($enrollments->where('progress', 100)->count() == 0)
                        <div class="flex flex-col items-center my-5 fle space-y-2">
                            <img src="/assets/svg/completion_empty.svg" alt="empty-img" class="md:w-[30%] -my-6">
                            <h2 class="text-center text-lg text-slate-400">{{ __('student.no_completed_courses') }}</h2>
                        </div>
                    @endif
                <div class="my-3 flex flex-wrap gap-5">
                    @foreach ($enrollments->where('progress', 100) as $enrollment)
                        <? if(!$enrollment->relationLoaded('course')){
                            $enrollment->load(['course' => function ($query) {
                                $query->withCount('lessons');
                            },'course.media', 'course.category', 'course.tutor', 'course.ratings']);
                        }?>
                        <x-my_courses.my-course-card :enrollment="$enrollment" type="completed">
                            <a href="{{ route('certificate_by_course', $enrollment->course) }}"
                                class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 py-2.5 text-xs font-bold text-emerald-600 transition border hover:bg-emerald-100 active:scale-[0.98]">
                                <i class="icon-[mdi--seal] size-4 "></i>
                                <span>{{ __('student.view_certificate') }}</span>
                            </a>
                        </x-my_courses.my-course-card>
                    @endforeach
                </div>
            </div>
        @elseif ($activeTab === 'saved')
            <div>
                <span class="font-bold me-2">{{ __('student.totals.wishlist') }}</span> —
                {{ __('student.totals.wishlist_caption') }}
                <div class="my-3 flex flex-wrap gap-5">
                    @foreach ($wishlistCourses as $record)
                        <x-my_courses.my-course-card :enrollment="$record" type="wishlist">
                            <div class="flex justify-between">
                                @if ($record->course->is_free)
                                    <div class="badge badge-primary text-white p-3 font-semibold">
                                        {{ __('course.free') }}
                                    </div>
                                @elseif($record->course->price > 0)
                                    <div class="badge badge-secondary p-3 font-semibold">
                                        {{ Number::currency($record->course->price) }}
                                    </div>
                                @endif
                                <livewire:wishlist-button :course="$record->course->toArray()" :is-wishlisted="true" :key="'wishlist-btn-' . $record->course->id" />
                            </div>
                        </x-my_courses.my-course-card>
                    @endforeach

                    <x-courses.empty-state />
                </div>
            @elseif ($activeTab === 'certificates')
                <div>
                    <p class="text-gray-600"><span
                            class="font-bold me-2">{{ __('student.totals.certificates') }}</span> —
                        {{ __('student.totals.certificates_caption') }}
                    </p>
                    <div class="my-4 grid gap-6 lg:grid-cols-3 md:grid-cols-2">
                        @foreach ($certificates as $certificate)
                            <? if(!$certificate->relationLoaded('course')){
                            $certificate->load(['course' => function ($query) {
                                $query->withCount('lessons');
                            },'course.media', 'course.category', 'course.tutor']);
                        }?>
                            {{-- <x-my_courses.certificate-card :certificate="$certificate" /> --}}
                            <x-my_courses.earned-certificate-card :certificate="$certificate" />
                        @endforeach
                    </div>
                    @if ($certificates->count() == 0)
                        <div class="flex flex-col items-center my-5 fle space-y-2">
                            <img src="/assets/svg/completion_empty.svg" alt="empty-img" class="md:w-[30%] -my-6">
                            <h2 class="text-center text-lg text-slate-400">{{ __('student.no_certificates') }}</h2>
                        </div>
                    @endif
                </div>
            @elseif ($activeTab === 'reviews')
                <div>
                    <p class="text-gray-600"><span class="font-bold me-2">{{ __('student.totals.my_reviews') }}</span>
                        — {{ __('student.totals.my_reviews_caption') }}</p>
                    @if ($ratings->count() == 0)
                        <div class="flex flex-col items-center my-5 fle space-y-2">
                            <img src="/assets/svg/empty_rating.svg" alt="guide-img" class="w-68">
                            <h2 class="text-xl text-slate-400">{{ __('student.no_ratings') }}</h2>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($ratings as $rating)
                            <x-my_courses.review-card :rating="$rating" />
                        @endforeach
                    </div>
                </div>

        @endif
    </div>
</div>
