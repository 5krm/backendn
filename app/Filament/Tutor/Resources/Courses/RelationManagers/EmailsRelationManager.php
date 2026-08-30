<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use App\Enums\CourseEmailType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Support\Components\Component;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Override;

class EmailsRelationManager extends RelationManager
{
    protected static string $relationship = 'emails';

    #[Override]
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {

        return __('course.emails._');
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // updated

                Section::make()
                    ->columns([
                        'lg' => 12,
                    ])
                    ->schema([
                        // Left side
                        Section::make(__('course.emails.config'))
                            ->columnSpan(8)
                            ->schema([
                                ToggleButtons::make('type')
                                    ->label(__('course.emails.fields.type'))
                                    ->options(
                                        collect(CourseEmailType::cases())
                                            ->reject(fn($enum) => in_array($enum, [
                                                CourseEmailType::NewCourse, CourseEmailType::PublishedLesson
                                            ]))                                            
                                            ->mapWithKeys(fn($enum) => [$enum->value => $enum->getLabel()])
                                            ->toArray()
                                    )
                                    ->default(CourseEmailType::welcome)
                                    ->colors(array_fill_keys(array_keys(CourseEmailType::class::cases()), 'primary'))
                                    ->grouped(),

                                TextInput::make('subject')->label(__('course.emails.fields.subject'))->live(),
                                Hidden::make('course_id')->default($this->ownerRecord->id),
                                Hidden::make('created_by')->default(auth()->id()),
                                RichEditor::make('body')
                                    ->label(__('course.emails.fields.body'))
                                    ->columnSpanFull()
                                    ->extraInputAttributes(['id' => 'course-content-editor'])
                                    ->live()
                                    ->default($this->getDefaultContent()),
                                Toggle::make('active')
                                    ->label(__('course.emails.fields.active'))
                                    ->onIcon('heroicon-m-check')
                                    ->offIcon('heroicon-m-x-mark')
                                    ->default(true),
                            ]),

                        // Right side
                        Grid::make()
                            ->columnSpan(4)
                            ->schema([
                                Section::make(__('course.emails.availableVars'))
                                    ->schema([
                                        ViewField::make('tags')
                                            ->view('filament.tutor.course_mails.available-tags')
                                            ->viewData(['tags' => $this->getTags()])
                                            ->dehydrated(false),
                                    ])
                                    ->columnSpanFull(),

                                Section::make(__('course.emails.preview'))
                                    ->schema([
                                        ViewField::make('preview')
                                            ->view('filament.tutor.course_mails.email-preview')
                                            ->viewData(fn($get) => [
                                                'subject' => $get('subject'),
                                                'body' => $get('body'),
                                                'tags' => $this->getTags(),
                                                'course' => $this->ownerRecord,
                                                'tutor' => auth()->user()
                                            ])
                                            ->dehydrated(false),
                                    ])->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->columns([
                TextColumn::make('subject')
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('tutor.table.type'))
                    ->badge(),

                ToggleColumn::make('active')
                    ->label(__('tutor.table.active')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->modalWidth('7xl')
                    ->label(__('course.emails.add_mail'))
                    ->modalHeading(__('course.emails.new_title')),
            ])
            ->recordActions([
                EditAction::make()->modalWidth('7xl'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTags(): array
    {
        return [
            'course_name' => '{course_name}',
            'tutor_name' => '{tutor_name}',
            'tutor_email' => '{tutor_email}',
            'course_url' => '{course_url}',
            'student_name' => '{student_name}'
        ];
    }
    function getDefaultContent()
    {
        return "<p>Dear {student_name},
            </p><p>Congrats on your achievement on the course <strong>{course_name}</strong>, Well Done!</p>
            <p>For any further questions please contact me at : {tutor_email}.</p>
            <p>keep it up!<br><br>Your instructor: {tutor_name}</p>";
    }
}
