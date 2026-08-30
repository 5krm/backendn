<x-layouts.auth>
    <div>
        <form method="POST" action="{{ route('auth.do-tutor-register') }}">
            @csrf
            <div class="card-body lg:w-5/6 mx-auto">
                <h2 class="card-title mb-4">{{ __('auth.tutor_signup') }}</h2>
                <x-session-status class="mb-4" :status="session('status')" />
                
                <x-base.input type="text" name="name" label="{{ __('auth.name') }}" />
                <x-base.input type="email" name="email" label="{{ __('auth.email') }}" />
                <x-base.password name="password" label="{{ __('auth.password') }}" />
                <x-base.password name="password_confirmation" label="{{ __('auth.password_confirm') }}" />

                <button disabled type="submit" class="btn btn-primary btn-sm action-button w-full mx-auto">
                    {{ __('auth.tutor_signup') }}
                </button>
                
                <p class="label-text flex justify-center"> {{ __('auth.register.have_account') }} <a
                        class="text-primary link ms-1 font-semibold"
                        href="{{ route('auth.login') }}">{{ __('auth.signin') }}</a></p>

                <div class="divider">{{ strtoupper(__('base.or')) }}</div>
                <div class="text-center">
                    <a href="/auth/google/redirect"
                        class="btn action-btn btn-sm  border border-[#ccc] flex items-center mb-3 w-full">
                        <object data="/assets/svg/google.svg" width="20" class="me-2"> </object>
                        {{ __('auth.sign_with', ['provider' => 'Google']) }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-layouts.auth>