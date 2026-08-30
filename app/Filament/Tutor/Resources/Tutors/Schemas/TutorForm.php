<?php

namespace App\Filament\Tutor\Resources\Tutors\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TutorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([

                Section::make(__('tutor.form.personal_info'))
                    ->icon('heroicon-o-user')
                    ->columnSpan(12)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('tutor.form.full_name'))
                            ->required(),

                        TextInput::make('email')
                            ->label(__('tutor.form.email_address'))
                            ->email()
                            ->required(),

                        TextInput::make('phone')
                            ->label(__('tutor.form.phone_number'))
                            ->tel(),

                        Select::make('country_id')
                            ->label(__('tutor.students.country'))
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('job_title')
                            ->label(__('profile.fields.job_title')),

                        TextInput::make('job_title_en')
                            ->label(__('profile.fields.job_title_en')),

                        Textarea::make('bio')
                            ->label(__('tutor.form.biography'))
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('bio_en')
                            ->label(__('tutor.form.biography_en'))
                            ->rows(4)
                            ->columnSpanFull(),

                        Hidden::make('is_tutor')
                            ->default(true),
                    ]),

                Section::make(__('tutor.form.professional_info'))
                    ->icon('heroicon-o-academic-cap')
                    ->relationship('tutorProfile')
                    ->columnSpan(12)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name_en')
                            ->label(__('tutor.form.name_en')),

                        TextInput::make('specialization')
                            ->label(__('tutor.form.specialization')),

                        TextInput::make('specialization_en')
                            ->label(__('tutor.form.specialization_en')),

                        TextInput::make('experience_years')
                            ->label(__('tutor.form.experience_years'))
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('hourly_rate')
                            ->label(__('tutor.form.hourly_rate'))
                            ->numeric()
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label(__('tutor.tutors.is_active'))
                            ->default(true),
                    ]),

                Section::make(__('tutor.form.media'))
                    ->icon('heroicon-o-photo')
                    ->columnSpan(12)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('profile')
                            ->label(__('profile.personal_info.change_avatar'))
                            ->collection('avatars')
                            ->avatar()
                            ->image()
                            ->imageEditor()
                    ]),
            ]);
    }
}
