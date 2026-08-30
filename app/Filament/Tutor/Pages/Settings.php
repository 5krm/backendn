<?php

namespace App\Filament\Tutor\Pages;

use App\Enums\SocialPlatform;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.settings');
    }

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.tutor.pages.settings';

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.profile');
    }

    public function getTitle(): string
    {
        return __('tutor.nav.settings');
    }

    public ?array $profileData = [];

    public function mount(): void
    {
        $user = auth()->user();
        $tutor = $user->tutorProfile;
        $user->load('socialLinks');

        $socialUrls = [];
        foreach (SocialPlatform::cases() as $platform) {
            $socialUrls[$platform->value] = null;
        }

        foreach ($user->socialLinks as $link) {
            $key = $link->platform?->value ?? ($link->getAttributes()['platform'] ?? null);
            if ($key && array_key_exists($key, $socialUrls)) {
                $socialUrls[$key] = $link->url;
            }
        }

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'job_title_en' => $user->job_title_en,
            'bio' => $user->bio,
            'bio_en' => $user->bio_en,
            'name_en' => $tutor?->name_en,
            'specialization' => $tutor?->specialization,
            'specialization_en' => $tutor?->specialization_en,
            'experience_years' => $tutor?->experience_years,
            'hourly_rate' => $tutor?->hourly_rate,
            'social_urls' => $socialUrls,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make(__('tutor.form.personal_info'))
                ->description(__('tutor.form.personal_info_desc'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('tutor.form.full_name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name_en')
                        ->label(__('tutor.form.name_en'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('tutor.form.email_address'))
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('tutor.form.phone_number'))
                        ->tel()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('job_title')
                        ->label(__('profile.fields.job_title'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('job_title_en')
                        ->label(__('profile.fields.job_title_en'))
                        ->maxLength(255),
                    Forms\Components\Textarea::make('bio')
                        ->label(__('tutor.form.biography'))
                        ->rows(4)
                        ->maxLength(5000)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('bio_en')
                        ->label(__('tutor.form.biography_en'))
                        ->rows(4)
                        ->maxLength(5000)
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make(__('tutor.form.professional_info'))
                ->description(__('tutor.form.professional_info_desc'))
                ->schema([
                    Forms\Components\TextInput::make('specialization')
                        ->label(__('tutor.form.specialization'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('specialization_en')
                        ->label(__('tutor.form.specialization_en'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('experience_years')
                        ->label(__('tutor.form.experience_years'))
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\TextInput::make('hourly_rate')
                        ->label(__('tutor.form.hourly_rate'))
                        ->numeric()
                        ->minValue(0),
                ])->columns(2),

            Section::make(__('tutor.form.social_media'))
                ->description(__('tutor.form.social_media_desc'))
                ->icon('heroicon-o-share')
                ->schema(
                    collect(SocialPlatform::cases())
                        ->map(fn (SocialPlatform $platform) => Forms\Components\TextInput::make("social_urls.{$platform->value}")
                            ->label($platform->getLabel())
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon($platform->getIcon())
                            ->placeholder('https://…')
                        )
                        ->all()
                )
                ->columns(2),

            Section::make(__('tutor.form.security'))
                ->description(__('tutor.form.security_desc'))
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label(__('tutor.form.new_password'))
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->same('password_confirmation')
                        ->dehydrated(fn ($state) => filled($state))
                        ->helperText(__('tutor.form.new_password_help')),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label(__('tutor.form.confirm_password'))
                        ->password()
                        ->revealable()
                        ->dehydrated(false),
                ])->columns(2),
        ])->statePath('profileData');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'job_title_en' => $data['job_title_en'] ?? null,
            'bio' => $data['bio'] ?? null,
            'bio_en' => $data['bio_en'] ?? null,
        ]);

        $tutor = $user->tutorProfile;
        if ($tutor) {
            $tutorUpdates = [
                'name_en' => $data['name_en'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'specialization_en' => $data['specialization_en'] ?? null,
            ];

            if (array_key_exists('experience_years', $data) && $data['experience_years'] !== null && $data['experience_years'] !== '') {
                $tutorUpdates['experience_years'] = $data['experience_years'];
            }

            if (array_key_exists('hourly_rate', $data) && $data['hourly_rate'] !== null && $data['hourly_rate'] !== '') {
                $tutorUpdates['hourly_rate'] = $data['hourly_rate'];
            }

            $tutor->update($tutorUpdates);
        }

        $keptPlatforms = [];
        foreach (SocialPlatform::cases() as $platform) {
            $url = trim((string) ($data['social_urls'][$platform->value] ?? ''));
            if ($url === '') {
                continue;
            }

            $keptPlatforms[] = $platform->value;
            $user->socialLinks()->updateOrCreate(
                ['platform' => $platform->value],
                ['url' => $url]
            );
        }

        $user->socialLinks()
            ->when(
                $keptPlatforms !== [],
                fn ($query) => $query->whereNotIn('platform', $keptPlatforms),
                fn ($query) => $query
            )
            ->delete();

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        Notification::make()
            ->title(__('tutor.form.profile_updated'))
            ->success()
            ->send();
    }
}
