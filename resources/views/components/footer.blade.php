<div class="bg-gradient-to-b from-gray-50 to-white border-t border-gray-200">
    <div class="container">
        <footer class="py-4">
            <!-- Main Footer Content -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Logos and Copyright -->
                <div class="flex flex-col items-center md:items-start gap-3">
                    <div class="flex items-end gap-4">
                        <a href="{{ route('courses') }}" class="block transition-transform hover:scale-105">
                            <img class="h-10 w-auto" src="{{ asset('assets/svg/logo/ngo-academy-logo-en.svg') }}"
                                alt="NGO Academy">
                        </a>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <a href="{{ app()->getLocale() === 'ar' ? 'https://portal365.org/ar' : 'https://portal365.org/' }}"
                            target="_blank" rel="noopener noreferrer" class="">
                            <span
                                class="text-xs text-gray-500 whitespace-nowrap">{{ __('base.Partnership_with') }}</span>
                            <img src="{{ asset(app()->getLocale() === 'ar' ? 'assets/images/portal ar.svg' : 'assets/images/portal en.svg') }}"
                                alt="Portal 365" class="h-6 w-auto block transition-transform hover:scale-105">
                        </a>
                    </div>
                </div>

                <!-- Legal Links -->
                <div>
                    <nav class="flex flex-wrap items-center justify-center gap-3 text-sm">
                        <a href="{{ route('legal.faq') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">
                            {{ __('base.faq') }}
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('legal.privacy-policy') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">
                            {{ __('base.privacy_policy') }}
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('legal.terms-of-service') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">
                            {{ __('base.terms_of_service') }}
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('legal.cookie-policy') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">
                            {{ __('base.cookie_policy') }}
                        </a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('legal.contact') }}"
                            class="text-gray-600 hover:text-primary transition-colors font-medium">
                            {{ __('base.contact_us') }}
                        </a>
                    </nav>
                    <p class="text-xs text-gray-500 w-full text-center mt-2 mb-0">
                        {{ __('home.copyright', ['year' => date('Y')]) }}</p>
                </div>
            </div>
        </footer>
    </div>
</div>
