<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use App\Enums\CourseStatus;
use App\Enums\Level;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class CourseInfoForm
{
    public static function schema(): array
    {
        return [
            Section::make()
                ->contained(false)
                ->schema([
                    TextInput::make('title')
                        ->label(__('tutor.form.course_title'))
                        ->required()
                        ->maxLength(190)
                        ->placeholder(__('tutor.form.course_title_placeholder'))
                        ->prefixIcon('heroicon-o-academic-cap')
                        ->columnSpanFull(),

                    Select::make('category_id')
                        ->label(__('tutor.form.category'))
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->prefixIcon('heroicon-o-tag')
                        ->placeholder(__('tutor.form.choose_category'))
                        ->native(false),

                    Select::make('lang')
                        ->label(__('tutor.form.language'))
                        ->options([
                            'ar' => __('tutor.form.arabic'),
                            'en' => __('tutor.form.english'),
                        ])
                        ->default('en')
                        ->required()
                        ->prefixIcon('heroicon-o-language')
                        ->native(false),

                    SpatieMediaLibraryFileUpload::make('cover_image')
                        ->label(__('tutor.form.course_card_image'))
                        ->collection('covers')
                        ->conversion('covers')
                        ->image()
                        ->maxSize(5120)
                        ->helperText(__('tutor.form.course_card_help'))
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label(__('tutor.form.course_description'))
                        ->required()
                        ->rows(4)
                        ->placeholder(__('tutor.form.course_description_placeholder'))
                        ->columnSpanFull(),

                    RichEditor::make('objectives')
                        ->label(__('tutor.form.learning_objectives'))
                        ->placeholder(__('tutor.form.learning_objectives_placeholder'))
                        ->columnSpanFull(),
                    Select::make('level')
                        ->label(__('tutor.form.level'))
                        ->options(Level::class)
                        ->required(),
                    Select::make('status')
                        ->label(__('tutor.form.status'))
                        ->options(CourseStatus::class)
                        ->required(),
                ])
                ->columns(2),
        ];
    }
}
