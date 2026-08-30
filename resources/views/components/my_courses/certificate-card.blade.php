<div class="card bg-base-100 card-bordered border-primary relative">
    <span class="icon-[mdi--certificate-outline] text-9xl absolute inset-0 opacity-[0.07] z-0"></span>
    <div class="card-body z-10 gap-0">
        <div class="flex items-center justify-between">
            <h2 class="card-title">
                <a href="{{ route('courses.details', $certificate->course) }}" class="hover:text-primary" target="_blank">
                    {{ $certificate->course->title }}
                </a>
            </h2>

            <div class="dropdown ">
                <button tabindex="0" class="btn btn-sm btn-circle">
                    <i class="icon-[mdi--ellipsis-vertical] text-xl"></i>
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
        <div class="flex items-center mt-0.5 text-slate-500">
            <span class="text-primary icon-[mdi--clock-time-four-outline]"></span>
            <span class="ms-2 text-sm"> {{ $certificate->course->textDuration }}
            </span>
        </div>

        <p class="mt-3">{{ __('dashboard.certificate_desc') }}</p>
        <div class="card-actions mt-6 justify-between">
            <span class="text-sm">{{ __('dashboard.earned_on') }}
                {{ $certificate?->issued_at?->format('d M, Y') }}</span>
            <a target="_blank" href="{{ route('app.courses.certificate', $certificate->course) }}"
                class="btn action-btn btn-sm btn-primary btn-outline flex items-center">
                {{ __('base.download') }}
                <span class="icon-[mdi--download]"></span>
            </a>
        </div>
    </div>
</div>
