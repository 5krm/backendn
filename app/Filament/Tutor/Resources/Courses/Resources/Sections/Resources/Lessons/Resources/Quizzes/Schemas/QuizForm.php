<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\Schemas;

use App\Models\Lessons\Lesson;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tutor_id')
                    ->default(fn () => auth()->user()->id),
                Hidden::make('course_id')
                    ->default(fn ($get) => $get('lesson_id')
                        ? Lesson::find($get('lesson_id'))->course_id
                        : null),
                Hidden::make('lesson_id')
                    ->default(fn ($get, $record) => $record?->lesson_id ?? null),

                TextInput::make('question')
                    ->label(__('tutor.form.question'))
                    ->required()
                    ->maxLength(190)
                    ->columnSpanFull()
                    ->reactive(),

                Textarea::make('description')
                    ->label(__('tutor.form.description'))
                    ->rows(3)
                    ->columnSpanFull(),
                Hidden::make('title')
                    ->default(fn ($get) => $get('question') ?? 'Untitled Quiz')
                    ->rules(['required']),

                Repeater::make('options')
                    ->relationship('quizOptions')
                    ->label(__('tutor.form.options'))
                    ->orderColumn('order')
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                $hasCorrect = collect($value)->contains(
                                    fn ($option) => ! empty($option['is_correct'])
                                );

                                if (! $hasCorrect) {
                                    $fail(__('tutor.form.optionRule'));
                                }
                            };
                        },
                    ])

                    ->table([
                        TableColumn::make(__('tutor.form.option_text'))->markAsRequired()->alignment(Alignment::Start),
                        TableColumn::make(__('tutor.form.correct'))->alignment(Alignment::Start),
                    ])
                    ->schema([
                        TextInput::make('value')
                            ->label(__('tutor.form.option_text'))
                            ->required(),
                        Toggle::make('is_correct')
                            ->label(__('tutor.form.correct')),
                    ])
                    ->minItems(2)
                    ->maxItems(6)
                    ->defaultItems(2)
                    ->addActionLabel(__('tutor.form.add_option'))
                    ->columnSpanFull(),
            ]);
    }
}
