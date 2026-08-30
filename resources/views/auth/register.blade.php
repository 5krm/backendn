<x-layouts.auth>
    <div class="w-full max-w-[550px] mx-auto">
        <div class="mb-10">
            <h2 class="text-3xl font-bold text-[#1A1A1A] mb-2">{{ __('auth.register.title') }}</h2>
            <p class="text-[#6B7280]">{{ __('auth.register.subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('auth.do-register') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-base.input name="name" label="{{ __('auth.name') }}" />
                <x-base.input dir="ltr" type="email" value="{{ Request::query('email') ?? '' }}" name="email"
                    label="{{ __('auth.email') }}" placeholder="name@example.com" />
            </div>

            <x-base.select name="country_id" :items="$countryItems" label="{{ __('auth.country') }}" :value="old('country_id')" />

            <x-base.phone name="phone" :countries="$countries" label="{{ __('auth.phone') }}" :value="old('phone')"
                dir="ltr" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-base.password name="password" label="{{ __('auth.password') }}" />
                <x-base.password name="password_confirmation" label="{{ __('auth.password_confirm') }}" />
            </div>

            <button type="submit"
                class="w-full py-4 bg-[#111827] text-white font-bold rounded-xl hover:bg-black transition-all shadow-lg shadow-gray-200 action-button"
                data-recaptcha="{{ config('app.recaptcha.id') }}" data-action="register">
                {{ __('auth.register.btn') }}
            </button>

            <p class="text-center text-[11px] text-[#9CA3AF] leading-relaxed">
                {{ __('auth.recaptcha_protected') }} {{ __('auth.and') }}
                <a href="https://policies.google.com/privacy"
                    class="underline hover:text-gray-600">{{ __('auth.privacy_policy') }}</a>
                {{ __('auth.and') }}
                <a href="https://policies.google.com/terms"
                    class="underline hover:text-gray-600">{{ __('auth.terms_of_service') }}</a>
                {{ __('auth.apply') }}
            </p>

            <div class="relative flex items-center justify-center py-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <span class="relative px-4 bg-white text-[10px] font-bold text-[#9CA3AF] uppercase tracking-widest">
                    {{ __('auth.login.or_continue_with') }}
                </span>
            </div>

            <a href="/auth/google/redirect"
                class="w-full flex items-center justify-center gap-3 py-3.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all font-medium text-[#1A1A1A]">
                <img src="/assets/svg/google.svg" alt="Google" class="w-5 h-5">
                {{ __('auth.google_continue') }}
            </a>

            <div class="text-center mt-10">
                <p class="text-sm text-[#6B7280]">
                    {{ __('auth.register.have_account') }}
                    <a href="{{ route('auth.login') }}" class="text-[#2DD4BF] font-bold hover:underline ms-1">
                        {{ __('auth.signin') }}
                    </a>
                </p>
            </div>
            @error('recaptcha')
                <div class="label">
                    <span class="label-text-alt text-red-500">{{ $message }} </span>
                </div>
            @enderror
        </form>
    </div>
</x-layouts.auth>
