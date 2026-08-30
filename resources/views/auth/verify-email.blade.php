<x-layouts.auth>
    <div class="px-4  mt-16">
        <h2 class=" card-title mb-6">{{ __('auth.account-verification.title') }} </h2>
        <div class="mb-4 text-sm text-gray-600">
            {{ __('auth.account-verification.sub_title') }}
        </div>


        @if (session('status') == 'verification-link-sent')
        <x-session-status class="mb-4" :status="__('auth.account-verification.note')" />
        @endif
        <div class="card-actions mt-8  flex justify-between items-baseline">

            <a href="{{ route('dashboard') }}" class="text-sm link">
                {{ __('auth.account-verification.skip') }}
            </a>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <button class="btn btn-primary btn-xs  md:btn-sm">
                        {{ __('auth.account-verification.btn') }}
                    </button>
                </div>

            </form>

        </div>


    </div>
</x-layouts.auth>
