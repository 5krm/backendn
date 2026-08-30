<x-layouts.app :title="$certificate
    ? $certificate->template_data['course_title'] ?? $certificate->course?->title
    : __('certificates.verify_title')" :metaDescription="__('seo.certificate_verify_description')">
    @php
        $isRtl = ($direction ?? 'ltr') === 'rtl';
        $isValid = $state === 'valid';
        $course = $certificate?->course;
        $courseTitle = $certificate ? $certificate->template_data['course_title'] ?? $course?->title : null;
        $organization = $course?->tutor?->organization;
        $organizationName = $organization?->name ?? __('certificates.default_organization');
        $organizationLogo = $organization?->logo_url;
        $stampPath = $organization?->stamp_url;
        $student = $certificate?->user;
        $studentName = $student?->name;
        $avatarUrl = $student?->profile;
        $initial = $studentName ? mb_strtoupper(mb_substr($studentName, 0, 1)) : '';
        $coverUrl = $course?->cover_image;
        $verifyUrl = $certificate?->verificationUrl();
        $academyLogo = asset('assets/svg/logo/ngo-academy-logo-en.svg');

        $tutorName = $course?->tutor?->tutorProfile?->localized_name ?? $course?->tutor?->name;
        $issueDate = $certificate?->issued_at?->translatedFormat('M d, Y');
    @endphp
    <div dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="pb-16 -mt-4">
        @if (!$certificate || !$isValid)
            {{-- Invalid / not found --}}
            <div class="   mx-auto pt-8">
                <div class="bg-white border border-gray-200 rounded-lg p-8 md:p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 mb-5">
                        <i class="icon-[mdi--close-circle] text-4xl text-red-500"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-[#000033] mb-3">
                        {{ __('certificates.not_valid') }}
                    </h1>
                    <p class="text-gray-600 text-base max-w-md mx-auto">
                        @if ($state === 'revoked')
                            {{ __('certificates.revoked_message') }}
                        @elseif ($state === 'expired')
                            {{ __('certificates.expired_message') }}
                        @elseif ($state === 'not_found')
                            {{ __('certificates.not_found_message') }}
                        @else
                            {{ __('certificates.invalid_message') }}
                        @endif
                    </p>
                    @if ($certificate)
                        <p class="mt-6 text-sm text-gray-400 font-mono">
                            {{ $certificate->verification_code }}
                        </p>
                    @endif
                </div>
            </div>
        @else
            {{-- Valid — Coursera-style layout --}}
            <div class="max-w-6xl mx-auto pt-6 md:pt-10">
                <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">

                    {{-- Left: accomplishment details --}}
                    <div class="lg:col-span-7 text-start">
                        <h1 class="text-3xl md:text-4xl font-bold text-[#000033] leading-tight tracking-tight mb-6">
                            {{ $courseTitle }}
                        </h1>

                        <div class="flex items-center gap-3 mb-5">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $studentName }}"
                                    class="h-12 w-12 rounded-full object-cover bg-gray-200 border border-gray-100">
                            @else
                                <div
                                    class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary text-lg font-bold">
                                    {{ $initial }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500">{{ __('certificates.completed_by') }}</p>
                                <p class="text-lg font-semibold text-[#000033]">{{ $studentName }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 mb-6">
                            @if ($certificate->completed_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="icon-[mdi--calendar-check] size-4 text-primary"></i>
                                    {{ $certificate->completed_at->translatedFormat('F j, Y') }}
                                </span>
                            @endif
                            @if ($hours)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="icon-[mdi--clock-outline] size-4 text-primary"></i>
                                    {{ __('certificates.hours_approx', ['hours' => $hours]) }}
                                </span>
                            @endif
                        </div>

                        <div
                            class="flex items-start gap-3 p-4 md:p-5 rounded-lg bg-teal-50 border border-teal-100 mb-8">
                            <i class="icon-[mdi--check-decagram] text-2xl text-primary shrink-0 mt-0.5"></i>
                            <p class="text-[#000033] text-base leading-relaxed">
                                {{ __('certificates.verified_statement', [
                                    'name' => $studentName,
                                    'org' => $organizationName,
                                    'course' => $courseTitle,
                                ]) }}
                            </p>
                        </div>
                        @if ($certificate->user_id == auth()?->id())
                            <div class="flex flex-wrap items-center gap-3 mb-8">
                                <span
                                    class="text-sm font-medium text-gray-500 me-1">{{ __('certificates.share') }}</span>
                                <a href="{{ $certificate->shareLink() }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-gray-200 hover:bg-[#0A66C2] hover:border-[#0A66C2] hover:text-white text-[#0A66C2] transition-colors"
                                    title="{{ __('certificates.share_linkedin') }}">
                                    <i class="icon-[mdi--linkedin] size-5"></i>
                                </a>
                                <button type="button" id="copy-verify-link" data-url="{{ $verifyUrl }}"
                                    data-copied="{{ __('certificates.link_copied') }}"
                                    class="inline-flex items-center gap-2 px-3 h-10 rounded-full border border-gray-200 text-sm text-gray-700 hover:border-primary hover:text-primary transition-colors">
                                    <i class="icon-[mdi--link-variant] size-4"></i>
                                    <span>{{ __('certificates.copy_link') }}</span>
                                </button>
                            </div>
                        @endif
                        <dl class="grid sm:grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-6">
                            <div>
                                <dt class="text-gray-400 mb-1">{{ __('certificates.credential_id') }}</dt>
                                <dd class="font-mono text-[#000033] font-medium">{{ $certificate->certificate_number }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 mb-1">{{ __('certificates.verification_code') }}</dt>
                                <dd class="font-mono text-[#000033] font-medium">{{ $certificate->verification_code }}
                                </dd>
                            </div>
                            @if ($certificate->issued_at)
                                <div>
                                    <dt class="text-gray-400 mb-1">{{ __('certificates.issued') }}</dt>
                                    <dd class="text-[#000033]">
                                        {{ $certificate->issued_at->translatedFormat('F j, Y') }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-gray-400 mb-1">{{ __('certificates.organization') }}</dt>
                                <dd class="text-[#000033] flex items-center gap-2">
                                    @if ($organizationLogo)
                                        <img src="{{ $organizationLogo }}" alt="{{ $organizationName }}"
                                            class="h-6 w-auto object-contain">
                                    @endif
                                    {{ $organizationName }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Right: certificate preview card (mirrors PDF design) --}}
                    <div class="lg:col-span-5">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden sticky top-24">
                            <div class="p-2 sm:p-3">
                                <div class="border-[6px] border-black aspect-[960/615] relative overflow-hidden"
                                    style="background-image: url('{{ asset('assets/images/certificate-background.png') }}'); background-size: cover; background-position: center;">
                                    <div class="absolute inset-0 p-3 sm:p-4 flex flex-col">
                                        {{-- Header: NGO Academy system logo --}}
                                        <div class="flex items-start justify-between shrink-0">
                                            <img src="{{ $organizationLogo }}" alt="NGO Academy"
                                                class="h-7 sm:h-9 w-auto object-contain">
                                            <div></div>
                                            <img src="{{ $academyLogo }}" alt="NGO Academy"
                                                class="h-7 sm:h-9 w-auto object-contain">
                                        </div>

                                        {{-- Body --}}
                                        <div
                                            class="flex-1 flex flex-col items-center justify-center text-center px-1 sm:px-2 min-h-0">
                                            <p
                                                class="capitalize text-[10px] sm:text-[8px] font-medium text-[#000033] mb-0.5">
                                                {{ __('course.certificate.title') }}
                                            </p>
                                            <p class="capitalize text-[10px] sm:text-[8px] text-gray-700 mb-1 sm:mb-2">
                                                {{ __('course.certificate.certify') }}
                                            </p>
                                            <p
                                                class="text-[#00cc99] font-bold text-base sm:text-xl leading-tight mb-1 sm:mb-2 line-clamp-2">
                                                {{ $studentName }}
                                            </p>
                                            @if ($hours)
                                                <p class="text-[10px] sm:text-xs text-gray-700">
                                                    {{ __('course.certificate.completed_hours', ['hours' => $hours]) }}
                                                </p>
                                            @endif
                                            <p class="text-[10px] sm:text-xs text-gray-700 mb-1">
                                                {{ __('course.certificate.of_training') }}
                                            </p>
                                            <p
                                                class="text-[11px] sm:text-sm font-semibold text-[#000033] leading-snug line-clamp-2">
                                                {{ $courseTitle }}
                                            </p>
                                        </div>

                                        {{-- Footer --}}
                                        <div
                                            class="grid grid-cols-3 gap-1 sm:gap-2 items-end text-[9px] sm:text-[11px] text-[#000033] shrink-0 pt-1">
                                            <div class="text-start">
                                                <p class="mb-0 text-gray-500 leading-tight text-[10px] sm:text-[8px]">
                                                    {{ __('course.certificate.issue_date') }}</p>
                                                <p class="font-medium leading-tight text-[6px] sm:text-[8px] mt-1">
                                                    {{ $issueDate }}</p>
                                            </div>
                                            <div class="text-center">
                                                <img src="{{ $stampPath}}"
                                                    alt=""
                                                    class="h-6 sm:h-8 w-auto mx-auto object-contain mb-0.5 opacity-80">

                                                <p class="mb-0 text-gray-500 leading-tight text-[10px] sm:text-[8px]">
                                                    {{ __('course.certificate.tutor') }}</p>
                                                <p class="font-medium leading-tight line-clamp-1 mt-1">
                                                    {{ $tutorName }}</p>
                                            </div>
                                            <div class="text-end">
                                                <p class="mb-0 text-gray-500 leading-tight text-[10px] sm:text-[8px]">
                                                    {{ __('course.certificate.number') }}</p>
                                                <p
                                                    class="font-medium font-mono leading-tight text-[6px] sm:text-[8px] break-all mt-1">
                                                    {{ $certificate->certificate_number }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($certificate->user_id == auth()?->id())
                                <div class="px-4 pb-4 space-y-3">
                                    <a href="{{ route('certificate.download', $certificate) }}" target="_blank"
                                        class="btn btn-primary btn-block btn-sm w-full">
                                        <i class="icon-[mdi--tray-arrow-down] size-5"></i>
                                        {{ __('certificates.download') }}
                                    </a>
                                    <a href="{{ $certificate->addToLinkedin() }}" target="_blank" rel="noopener"
                                        class="btn btn-sm w-full border-0 text-white"
                                        style="background-color: #0A66C2;">
                                        <i class="icon-[mdi--linkedin] size-5"></i>
                                        {{ __('certificates.add_to_linkedin') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Course card (Coursera-style lower section) --}}
                @if ($course)
                    <div class="mt-12 md:mt-16 pt-10 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-[#000033] mb-5">{{ __('certificates.about_course') }}</h2>
                        <div
                            class="bg-white border border-gray-200 rounded-xl overflow-hidden flex flex-col sm:flex-row">
                            <div class="sm:w-56 shrink-0 bg-gray-100">
                                <img src="{{ $coverUrl ?: asset('assets/images/default-course.png') }}"
                                    alt="{{ $courseTitle }}" class="w-full h-40 sm:h-full object-cover">
                            </div>
                            <div
                                class="p-5 md:p-6 flex-1 flex flex-col sm:flex-row sm:items-center gap-4 justify-between text-start">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        @if ($organizationLogo)
                                            <img src="{{ $organizationLogo }}" alt=""
                                                class="h-6 w-auto object-contain">
                                        @endif
                                        <span class="text-sm text-gray-500">{{ $organizationName }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-[#000033] mb-1">{{ $courseTitle }}</h3>
                                    @if ($course->description)
                                        <p class="text-sm text-gray-600 line-clamp-2">
                                            {{ Str::limit(strip_tags($course->description), 140) }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('courses.details', $course) }}"
                                    class="btn btn-primary btn-sm shrink-0 self-start sm:self-center">
                                    {{ __('certificates.view_course') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <script>
                document.getElementById('copy-verify-link')?.addEventListener('click', async function() {
                    const url = this.dataset.url;
                    const label = this.querySelector('span');
                    const original = label.textContent;
                    try {
                        await navigator.clipboard.writeText(url);
                        label.textContent = this.dataset.copied;
                        setTimeout(() => {
                            label.textContent = original;
                        }, 2000);
                    } catch (e) {
                        window.prompt('', url);
                    }
                });
            </script>
        @endif
    </div>
</x-layouts.app>
