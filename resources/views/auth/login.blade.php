<x-layouts.auth>
    <div class="w-full max-w-[420px] mx-auto">
        <div class="mb-10">
            <h2 class="text-3xl font-bold text-[#1A1A1A] mb-2">{{ __('auth.login.welcome') }}</h2>
            <p class="text-[#6B7280]">{{ __('auth.login.subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('auth.do-login') }}" class="space-y-4">
            @csrf
            <x-session-status class="mb-4" :status="session('status')" />

            <x-base.input type="email" name="email" label="{{ __('auth.email') }}" placeholder="name@example.com"
                dir="ltr" />

            <div class="relative">

                <x-base.password name="password" label="{{ __('auth.password') }}" />
                @if (Route::has('password.request'))
                    <a class="text-[13px] font-semibold text-[#2DD4BF] hover:underline mt-3 block"
                        href="{{ route('password.request') }}">
                        {{ __('auth.forgot-password.title') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2 py-1">
                <input type="checkbox" name="remember" id="remember"
                    class="checkbox checkbox-sm checkbox-primary rounded-md border-gray-300" checked />
                <label for="remember" class="text-sm font-medium text-[#1A1A1A] cursor-pointer">
                    {{ __('auth.login.keep_me_logged_in') }}
                </label>
            </div>

            <button type="submit"
                class="action-button w-full py-2 bg-primary text-white font-bold rounded-xl hover:bg-[#00b184] transition-all shadow-lg shadow-gray-200"
                data-recaptcha="{{ config('app.recaptcha.id') }}" data-action="login">
                {{ __('auth.signin') }}
            </button>
            @error('recaptcha')
                <div class="label">
                    <span class="label-text-alt text-red-500">{{ $message }} </span>
                </div>
            @enderror
            <p class="text-center text-[11px] text-[#9CA3AF] leading-relaxed">
                {{ __('auth.recaptcha_protected') }} {{ __('auth.and') }}
                <a href="https://policies.google.com/privacy"
                    class="underline hover:text-gray-600">{{ __('auth.privacy_policy') }}</a>
                {{ __('auth.and') }}
                <a href="https://policies.google.com/terms"
                    class="underline hover:text-gray-600">{{ __('auth.terms_of_service') }}</a>
                {{ __('auth.apply') }}
            </p>

            <div class="relative flex items-center justify-center py-2 mt-0">
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
                    {{ __('auth.no_account') }}
                    <a href="{{ route('auth.register') }}" class="text-[#2DD4BF] font-bold hover:underline ms-1">
                        {{ __('auth.login.create_account') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-layouts.auth>
