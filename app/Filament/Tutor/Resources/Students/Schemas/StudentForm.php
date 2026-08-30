<?php

namespace App\Filament\Tutor\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
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
                    ]),

                Section::make(__('tutor.form.learning_profile'))
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpan(12)
                    ->columns(2)
                    ->schema([
                        Select::make('organization_id')
                            ->label(__('tutor.form.organization_name'))
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
            ]);
    }
}
