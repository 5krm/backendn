<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class LessonForm
{
    public static function schema(): array
    {
        return [
            Hidden::make('course_id')
                ->default(fn($livewire) => $livewire->record?->id),

            Hidden::make('section_id')
                ->default(fn($get, $livewire) => $get('../../id')),

            TextInput::make('title')
                ->label(__('tutor.form.lesson_title'))
                ->required()
                ->maxLength(190)
                ->placeholder(__('tutor.form.lesson_title_placeholder'))
                ->columnSpanFull(),

            RichEditor::make('content')
                ->label(__('tutor.form.lesson_content'))
                ->required()
                ->placeholder(__('tutor.form.lesson_content_placeholder'))
                ->columnSpanFull(),

            TextInput::make('video_url')
                ->label(__('tutor.form.video_url'))
                ->url()
                ->placeholder(__('tutor.form.video_url_placeholder'))
                ->columnSpanFull(),

            TextInput::make('duration')
                ->label(__('tutor.form.duration'))
                ->numeric()
                ->suffix(__('tutor.form.duration_minutes'))
                ->required(),

            Toggle::make('status')
                ->label(__('tutor.form.published'))
                ->default(false)
                ->onIcon('heroicon-o-eye')
                ->offIcon('heroicon-o-eye-slash')
                ->extraAttributes([
                    'class' => 'publish-toggle',
                ]),

            Repeater::make('quizzes')
                ->relationship('quizzes')
                ->label(__('tutor.form.quizzes'))

                ->extraFieldWrapperAttributes([
                    'class' => 'quizzes-wrapper'
                ])
                ->schema([
                    Hidden::make('course_id')
                        ->default(fn($livewire) => $livewire->record?->id),

                    Hidden::make('lesson_id')
                        ->default(fn($get) => $get('../../id')),

                    Hidden::make('tutor_id')
                        ->default(fn() => auth()->user()->id),

                    TextInput::make('question')
                        ->label(__('tutor.form.quiz_question'))
                        ->required()
                        ->placeholder(__('tutor.form.quiz_question_placeholder'))
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label(__('tutor.form.question_description'))
                        ->rows(2)
                        ->columnSpanFull(),

                    Repeater::make('quizOptions')
                        ->relationship('quizOptions')
                        ->label(__('tutor.form.answer_options'))
                        ->extraFieldWrapperAttributes([
                            'class' => 'quiz-options-wrapper'
                        ])
                        ->schema([
                            Hidden::make('id'),

                            TextInput::make('value')
                                ->label(__('tutor.form.option_text'))
                                ->required()
                                ->placeholder(__('tutor.form.option_placeholder')),

                            Toggle::make('is_correct')
                                ->label(__('tutor.form.correct_answer'))
                                ->default(false)
                                ->helperText(__('tutor.form.correct_answer_help')),

                            Hidden::make('order')
                                ->default(fn($get) => count($get('../../quizOptions') ?? []) + 1),
                        ])
                        ->columns(2)
                        ->minItems(2)
                        ->maxItems(6)
                        ->defaultItems(4)
                        ->addActionLabel(__('tutor.form.add_option'))
                        ->columnSpanFull()
                        ->collapsible()
                        ->itemLabel(fn(array $state): ?string => $state['value'] ?? __('tutor.form.add_option')),
                ])
                ->columns(3)
                ->itemLabel(fn(array $state): ?string => $state['title'] ?? __('tutor.form.new_quiz'))
                ->collapsed()
                ->collapsible()
                ->addActionLabel(__('tutor.form.add_quiz'))
                ->defaultItems(0)
                ->orderColumn('order')
                ->reorderable(true)
                ->columnSpanFull(),
        ];
    }
}
