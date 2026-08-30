<x-layouts.profile :user="$user">
    <div class="lg:grid lg:gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1 pb-5">
            <h4 class="font-bold text-lg">{{ __('settings.display_settings.title') }} </h4>
            <p class="text-gray-600 text-sm">{{ __('settings.display_settings.subtitle') }}</p>
        </div>
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.settings.update-display-language') }}" class="space-y-3">

                @csrf
                @method('PUT')
                <x-base.select name="displayLanguage" :items="App\Enums\Language::getListItems()" label="{{__('settings.display_settings.select_display_language')}}" :value="$preferences['display_language']"></x-base.select>

                <x-base.select name="learningLanguage" :items="App\Enums\Language::getListItems()" label="{{__('settings.display_settings.select_learning_language')}}" :value="$preferences['learning_language']"></x-base.select>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-sm btn-primary mt-4 action-button">
                        {{ __('base.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="divider lg:my-12 my-8"></div>
    <div class="lg:grid lg:gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1 pb-5">
            <h4 class="font-bold text-lg">{{ __('settings.email_preferences.title') }}</h4>
            <p class="text-gray-600 text-sm">{{ __('settings.email_preferences.subtitle') }}</p>
        </div>
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.settings.update-email-preferences') }}" class="space-y-3">
                @csrf
                @method('PUT')

                <x-base.checkbox size="sm" label="{{__('settings.email_preferences.followup')}}" name="receiveFollowupEmails" :value="$preferences['followup_email']"></x-base.checkbox>

                <div class="divider"></div>
                <x-base.checkbox size="sm" label="{{__('settings.email_preferences.comments')}}" name="receiveNotificationEmails" :value="$preferences['notification_email']"></x-base.checkbox>
                <div class="divider"></div>
                <x-base.checkbox size="sm" label="{{__('settings.email_preferences.course_updates')}}" name="receiveUpdatesEmails" :value="$preferences['update_email']"></x-base.checkbox>
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-sm btn-primary mt-4 action-button">
                        {{ __('base.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.profile>
