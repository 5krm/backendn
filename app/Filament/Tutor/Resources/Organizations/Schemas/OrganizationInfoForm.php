<?php

namespace App\Filament\Tutor\Resources\Organizations\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class OrganizationInfoForm
{
    public static function schema(): array
    {
        return [
            Section::make()
                ->contained(false)
                ->schema([
                    TextInput::make('name')
                        ->label(__('tutor.form.organization_name'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label(__('tutor.form.slug'))
                        ->required()
                        ->unique(ignoreRecord: true),

                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label(__('tutor.form.organization_logo'))
                        ->collection('logo')
                        ->image()
                        ->imageEditor(),

                    SpatieMediaLibraryFileUpload::make('stamp')
                        ->label(__('tutor.form.organization_stamp'))
                        ->collection('stamp')
                        ->image()
                        ->imageEditor(),

                    Textarea::make('description')
                        ->label(__('tutor.form.description'))
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label(__('tutor.form.active'))
                        ->default(true),
                ])
                ->columns(2),

            Section::make(__('tutor.form.organization_public_profile'))
                ->description(__('tutor.form.organization_public_profile_desc'))
                ->schema([
                    TextInput::make('website')
                        ->label(__('tutor.form.website'))
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://example.com'),

                    TextInput::make('category')
                        ->label(__('tutor.form.category'))
                        ->maxLength(255)
                        ->placeholder(__('tutor.form.organization_category_placeholder')),

                    TextInput::make('founded')
                        ->label(__('tutor.form.founded'))
                        ->numeric()
                        ->minValue(1800)
                        ->maxValue((int) date('Y')),

                    TextInput::make('position')
                        ->label('Position')
                        ->maxLength(255)
                        ->placeholder('Riyadh, Saudi Arabia'),
                ])
                ->columns(2),
        ];
    }
}
