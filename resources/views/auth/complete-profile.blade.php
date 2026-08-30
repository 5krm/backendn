<x-layouts.auth>
    <form method="POST" action="{{ route('auth.complete-profile.store') }}">
        @csrf
        <div class="card-body mx-auto">
            <h2 class="card-title mb-1">{{ __('auth.complete_profile.title') }}</h2>
            <p class="text-sm text-base-content/60 mb-4">{{ __('auth.complete_profile.subtitle') }}</p>

            <x-base.select name="country_id" :items="$countryItems" label="{{ __('auth.country') }}"
                :value="old('country_id')"></x-base.select>

            <x-base.phone name="phone" :countries="$countries" label="{{ __('auth.phone') }}"
                :value="old('phone')"></x-base.phone>

            <button type="submit" class="btn btn-primary action-button w-full mx-auto mt-2">
                {{ __('auth.complete_profile.btn') }}
            </button>
        </div>
    </form>
</x-layouts.auth>
