<x-layouts.auth>

    <div class=" mt-6">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="card-body">

                <h2 class="card-title mb-4">{{ __('auth.forgot-password.title') }}</h2>
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('auth.forgot-password.sub_title') }}
                </div>

                <x-session-status class="mb-4" :status="session('status')" />
                <x-base.input type="email" name="email" label="{{ __('auth.email') }}" :value="old('email')" />
                <div class="card-actions mt-4 justify-between items-center">
                    <a href="{{ route('auth.login') }}" class="text-sm link">
                        {{ __('auth.back_login') }}
                    </a>
                    <input type="submit" value="{{ __('auth.forgot-password.btn') }}"
                        class="btn btn-primary  btn-xs  md:btn-sm " />

                </div>
            </div>
        </form>
    </div>







</x-layouts.auth>
