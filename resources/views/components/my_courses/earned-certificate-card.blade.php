<div
    class="max-w-sm rounded-2xl bg-white  shadow-sm transform-gpu transition-all duration-700 ease-out hover:-translate-y-1 hover:scale-[1.01] hover:shadow-lg will-change-transform group border border-primary/20 font-sans">
    <div class="relative h-44  p-4 rounded-2xl overflow-hidden  rounded-b-none flex items-start gap-3">
        <img src="{{ $certificate->course->cover_image }}" alt="Course Background"
            class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-teal-50/70 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] ">
        </div>
        <div class="flex items-center h-full">
            <i class="icon-[mdi--certificate-outline] size-20 text-teal-500"></i>
            <div class="relative z-10 pt-1">
                <span
                    class="block text-sm font-bold tracking-wider text-teal-600 uppercase">{{ __('student.certificate') }}</span>
                <span class="block text-lg font-bold text-teal-700">{{ __('student.of_completion') }}</span>
            </div>
        </div>
    </div>

    <div class="mt-3 space-y-3 p-6">
        <span class=" top-3 left-3 rounded-xl bg-primary/10 px-3 py-1 text-xs font-semibold text-primary shadow-sm">
            {{ $certificate->course->category->name }}
        </span>
        <h2 class="card-title group-hover:text-primary">
            <a href="{{ route('courses.details', $certificate->course) }}" class="hover:text-primary" target="_blank">
                {{ $certificate->course->title }}
            </a>
        </h2>

        <div class="space-y-2 text-sm text-gray-600 flex justify-between">
            @if (isset($certificate->course->organization))
            <a href="{{ route('organization.index', $certificate->course->organization) }}" target="_blank"
                class="hover:text-primary">
                <div class="flex items-center gap-2">                        
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 border">
                            <img src="{{ $certificate->course->organization->logo_url }}"
                            alt="{{ $certificate->course->organization->name }}" class="h-full w-full object-contain">
                        </div>
                        <span class="font-medium text-gray-500">{{ $certificate->course->organization->name }}</span>
                    </div>
                </a>
            @endif
            @if ($certificate->course->duration)
                <div class="flex items-center mt-0.5 text-slate-500">
                    <span class="text-primary icon-[mdi--clock-time-four-outline]"></span>
                    <span class="ms-2 text-sm"> {{ $certificate->course->textDuration }}
                    </span>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 pb-2">
            <x-my_courses.donut-score :score="$certificate->score" />

            <div class="text-end ">
                <p class="text-xs text-gray-400">{{ __('dashboard.earned_on') }}</p>
                <p class="font-bold text-gray-900 text-sm">{{ $certificate?->issued_at?->format('d M, Y') }}</p>
            </div>
        </div>

        <div class=" space-y-2 px-0 my-1">
            <a target="_blank" href="{{ route('app.courses.certificate', $certificate->course) }}"
                class="col-span-2 btn action-btn btn-sm btn-primary btn-outline flex items-center  border border-primary/20 bg-primary/5 ">
                {{ __('base.download') }}
                <span class="icon-[mdi--download]"></span>
            </a>
            <div class="dropdown  w-full">
                <button tabindex="0" class="btn btn-sm rounded-lg flex px-3 items-center border-gray-300 w-full">
                    <i class="icon-[mdi--share-variant-outline] text-xl"></i> {{ __('student.share') }}
                </button>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-56 p-2 border shadow">
                    <li>
                        <a href="{{ $certificate->shareLink() }}" target="_blank">
                            <i class="icon-[mdi--linkedin] size-5"></i>
                            {{ __('course.shareToLinkedIn') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $certificate->addToLinkedin() }}" target="_blank">
                            <i class="icon-[mdi--linkedin] size-5"></i>
                            {{ __('course.addToLinkedInProfile') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
