<x-layouts.profile>
    <div class="lg:grid lg:gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1 pb-5">
            <h4 class="font-bold text-lg">{{ __('profile.personal_info.title') }}</h4>
            <p class="text-gray-600 text-sm">{{ __('profile.personal_info.subtitle') }}</p>
        </div>
        <div class="lg:col-span-2">
            <div class="flex items-center gap-10">
                <div class="avatar">
                    <div class="w-28 rounded-xl">
                        <img src="{{ $user->profile }}" />
                    </div>
                </div>
                <form method="POST" enctype="multipart/form-data" action="{{ route('app.profile.change-avatar') }}">
                    @csrf
                    @method('PUT')
                    <button id="changeAvatarBtn"
                        class="btn btn-secondary action-button">{{ __('profile.personal_info.change_avatar') }}</button>
                    <input id="avatarInput" name="avatar" type="file" class="hidden" />
                    @error('avatar')
                        <div class="label">
                            <span class="label-text-alt text-red-500">{{ $message }} </span>
                        </div>
                    @enderror
                    <p class="mt-3 text-xs text-gray-500">{{ __('validation.profile') }}</p>
                </form>
            </div>
            <form method="POST" action="{{ route('app.profile.update') }}"
                class="mt-10 space-y-3 grid sm:grid-cols-2 gap-2" id="profile-info-form">
                @csrf
                @method('PUT')

                <x-base.input name="name" label="{{ __('auth.name') }}" :value="$user->name" />
                <x-base.input :disabled="true" type="email" name="email" label="{{ __('auth.email') }}"
                    :value="$user->email" />

                <x-base.select class="col-span-2" name="country_id" :items="$countryItems" label="{{ __('auth.country') }}"
                    :value="$user->country_id"></x-base.select>

                <x-base.phone class="col-span-2" name="phone" :countries="$countries" label="{{ __('auth.phone') }}"
                    :value="$user->phone"></x-base.phone>

                <x-base.input name="job_title" label="{{ __('profile.fields.job_title') }}" :value="$user->job_title"
                    :optinal="true" />
                <x-base.input name="job_title_en" label="{{ __('profile.fields.job_title_en') }}" :value="$user->job_title_en"
                    :optinal="true" />

                <x-base.textarea class="col-span-2" name="bio" label="{{ __('profile.fields.bio') }}"
                    :value="$user->bio" :optinal="true" rows="4" />
                <x-base.textarea class="col-span-2" name="bio_en" label="{{ __('profile.fields.bio_en') }}"
                    :value="$user->bio_en" :optinal="true" rows="4" />

                <div class="col-span-2">
                    <div class="divider"></div>
                    <h5 class="font-semibold text-base">{{ __('profile.social_links.title') }}</h5>
                    <p class="text-gray-600 text-sm pb-2">{{ __('profile.social_links.subtitle') }}</p>

                    <div class="space-y-3">
                        @foreach ($socialPlatforms as $index => $platform)
                            @php
                                $existing = $socialByPlatform->get($platform->value);
                                $url = old("social_links.$index.url", $existing?->url);
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 shrink-0 flex items-center justify-center rounded-full bg-gray-100 text-gray-700"
                                    title="{{ $platform->getLabel() }}">
                                    <x-social-platform-icon :platform="$platform" class="w-5 h-5" />
                                </div>
                                <input type="hidden" name="social_links[{{ $index }}][platform]"
                                    value="{{ $platform->value }}" />
                                <label class="form-control w-full">
                                    <input type="url" name="social_links[{{ $index }}][url]"
                                        value="{{ $url }}" placeholder="{{ $platform->getLabel() }}"
                                        class="input input-bordered w-full @error('social_links.' . $index . '.url') input-error @enderror" />
                                    @error('social_links.' . $index . '.url')
                                        <div class="label">
                                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="flex justify-end col-span-2">
                    <button type="submit" class="btn btn-sm btn-primary mt-4 action-button">
                        {{ __('base.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="divider lg:my-12 my-8"></div>
    <div class="lg:grid lg:gap-8 lg:grid-cols-3">
        <div class="col-span-1 pb-5">
            <h4 class="font-bold text-lg">{{ __('profile.change_password.title') }}
            </h4>
            <p class="text-gray-600 text-sm">{{ __('profile.change_password.subtitle') }}</p>
        </div>
        <div class="col-span-2">
            <form method="POST" action="{{ route('app.profile.change-password') }}" class="space-y-3">
                @csrf
                @method('PUT')
                <x-base.input type="password" name="current_password" label="{{ __('auth.current_password') }}" />
                <x-base.input type="password" name="new_password" label="{{ __('auth.new_password') }}" />
                <x-base.input type="password" name="new_password_confirmation"
                    label="{{ __('auth.password_confirm') }}" />

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-sm btn-primary mt-4 action-button">
                        {{ __('base.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.profile>
