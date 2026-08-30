<x-layouts.auth>
    <div>
        <form method="POST" action="{{ route('password.store') }}">

            @if (session('status'))
            <div class='font-medium text-sm text-green-600 mt-6'>
                {{ session('status') }}
            </div>
            @endif
            @csrf
            <div class="card-body">
                <h2 class="card-title mb-4">{{ __('auth.reset-password.title') }}</h2>
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                @if($errors->any())
                <div class="flex justify-between p-4 mb-4 bg-red-100 rounded-lg " role="alert">
                    <p class="mt-2 text-sm text-red-600">
                        {{$errors->first()}}
                    </p>
                </div>
                @endif

                <x-base.input id="email" class="block mt-1 w-full" type="email" name="email"
                    label="{{ __('auth.email') }}" :value="old('email', $request->email)" required autofocus />
                <x-base.input id="password" class="block mt-1 w-full" type="password" name="password"
                    label="{{ __('auth.password') }}" />
                <x-base.input id="password_confirmation" class="block mt-1 w-full" type="password"
                    :label="__('auth.password_confirm')" name="password_confirmation" required />
                <div class="card-actions mt-4 justify-between items-center">
                    <a href="{{ route('auth.login') }}" class="text-sm link">
                        {{ __('auth.back_login') }}
                    </a>
                    <input type="submit" value="{{ __('auth.reset-password.btn') }}" class="btn btn-primary btn-sm" />


                </div>
            </div>
        </form>
    </div>
</x-layouts.auth>
