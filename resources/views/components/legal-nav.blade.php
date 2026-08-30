<nav class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <a href="{{ route('legal.faq') }}" 
       class="flex items-center justify-center px-4 py-3 rounded-lg border-2 transition-all {{ request()->routeIs('legal.faq') ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50 text-gray-700' }}">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-xs font-medium whitespace-nowrap">{{ __('base.faq') }}</span>
    </a>
    
    <a href="{{ route('legal.privacy-policy') }}" 
       class="flex items-center justify-center px-4 py-3 rounded-lg border-2 transition-all {{ request()->routeIs('legal.privacy-policy') ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50 text-gray-700' }}">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span class="text-xs font-medium whitespace-nowrap">{{ __('base.privacy_policy') }}</span>
    </a>
    
    <a href="{{ route('legal.terms-of-service') }}" 
       class="flex items-center justify-center px-4 py-3 rounded-lg border-2 transition-all {{ request()->routeIs('legal.terms-of-service') ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50 text-gray-700' }}">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span class="text-xs font-medium whitespace-nowrap">{{ __('base.terms_of_service') }}</span>
    </a>
    
     <a href="{{ route('legal.cookie-policy') }}" 
       class="flex items-center justify-center px-4 py-3 rounded-lg border-2 transition-all {{ request()->routeIs('legal.cookie-policy') ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50 text-gray-700' }}">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        <span class="text-xs font-medium text-center leading-tight">{{ __('base.cookie_policy') }}</span>
    </a>
    
    <a href="{{ route('legal.contact') }}" 
       class="flex items-center justify-center px-4 py-3 rounded-lg border-2 transition-all {{ request()->routeIs('legal.contact') ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50 text-gray-700' }}">
        <svg class="w-5 h-5 {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span class="text-xs font-medium whitespace-nowrap">{{ __('base.contact_us') }}</span>
    </a>
</nav>