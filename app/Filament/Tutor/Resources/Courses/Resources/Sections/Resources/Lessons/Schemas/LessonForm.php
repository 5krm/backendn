<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tutor.form.basic_info'))
                    ->description(__('tutor.form.basic_info_desc'))
                    ->schema([
                        Hidden::make('section_id'),
                        Hidden::make('course_id'),
                        TextInput::make('title')
                            ->label(__('tutor.form.lesson_title'))
                            ->required()
                            ->maxLength(190)
                            ->placeholder(__('tutor.form.lesson_title_placeholder'))
                            ->prefixIcon('heroicon-o-academic-cap'),
                        TextInput::make('duration')
                            ->label(__('tutor.form.duration'))
                            ->numeric()
                            ->suffix(__('tutor.form.duration_minutes'))
                            ->prefixIcon('heroicon-o-clock')
                            ->placeholder('30')
                            ->required()
                            ->helperText(__('tutor.form.duration_help')),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make(__('tutor.form.lesson_content_section'))
                    // ->description(__('tutor.form.lesson_content_desc'))
                    ->schema([
                        RichEditor::make('content')
                            ->label(__('tutor.form.lesson_content'))
                            ->required()
                            ->placeholder(__('tutor.form.lesson_content_placeholder'))
                            ->columnSpanFull(),
                        TextInput::make('video_id')
                            ->label(__('tutor.form.video_url'))
                            ->maxLength(255)
                            ->rules(['nullable', 'regex:/^https?:\/\/(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/)[\w\-]{11}/']),
                    ])
                    ->collapsible()->columnSpanFull(),

                Section::make(__('tutor.form.lesson_materials'))
                    ->description(__('tutor.form.lesson_materials_desc'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Repeater::make('resources')
                            ->label('')
                            ->relationship('resources', fn ($query) => $query->with('media'))
                            ->hiddenLabel()
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('tutor.form.resource_title'))
                                    ->required()
                                    ->maxLength(190)
                                    ->placeholder(__('tutor.form.resource_title_placeholder'))
                                    ->prefixIcon('heroicon-o-document-text'),
                                SpatieMediaLibraryFileUpload::make('file_path')
                                    ->label(__('tutor.form.file'))
                                    ->collection('resources')
                                    ->customProperties(fn (callable $get) => [
                                        'course_id' => $get('../../course_id') ?? 'temp',
                                    ])
                                    ->maxSize(5120)
                                    ->helperText(__('tutor.form.file_size_help'))
                                    ->acceptedFileTypes([
                                        'application/pdf',
                                        'image/*',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    ])
                                    ->downloadable(),
                            ])
                            ->addActionLabel(__('tutor.form.add_lesson_resource'))
                            ->columnSpanFull()
                            ->collapsible()
                            ->minItems(0),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
