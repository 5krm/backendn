<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\Courses\CourseTestimonial;

class TestimonialsRelationManager extends RelationManager
{
    protected static string $relationship = 'testimonials';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.resources.testimonials');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('tutor.form.testimonial_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('job_title')
                    ->label(__('tutor.form.testimonial_job_title'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')
                    ->label(__('tutor.form.testimonial_content'))
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('author_image')
                    ->label(__('tutor.form.testimonial_author_image'))
                    ->collection('authors')
                    ->avatar(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('author_image')
                    ->label(__('tutor.form.testimonial_author_image'))
                    ->collection('authors')
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('tutor.form.testimonial_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job_title')
                    ->label(__('tutor.form.testimonial_job_title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->label(__('tutor.form.testimonial_content'))
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('tutor.form.add_testimonial'))
                    ->modalHeading(__('tutor.form.new_testimonial')),
                Action::make('create_from_resource')
                    ->label(__('tutor.testimonials.create_from_resource'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->form([
                        \Filament\Forms\Components\Select::make('source_id')
                            ->options([
                                'course_ratings' => __('tutor.testimonials.from_course_ratings'),
                                'lesson_comments' => __('tutor.testimonials.from_lesson_comments'),
                            ])->live()
                            ->label(__('tutor.testimonials.select_source'))
                            ->afterStateUpdated(fn($set) => $set('comment_id', null)),
                        \Filament\Forms\Components\Select::make('comment_id')
                            ->label(__('tutor.form.select_comment'))
                            ->searchable()
                            ->required()
                            ->options(
                                function ($get, $set, $state) {
                                    if ($get('source_id') === 'course_ratings') {
                                        return \App\Models\Courses\CourseRating::query()
                                            ->with('user')
                                            ->latest()
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn($rating) => [
                                                $rating->id => $rating->review . ' - ' . $rating->user?->name
                                            ]);
                                    }
                                    return Comment::query()
                                        ->with('user')
                                        ->latest()
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn($comment) => [
                                            $comment->id => $comment->content . ' - ' . $comment->user?->name
                                        ]);
                                }
                            ),
                    ])
                    ->action(function (array $data, $livewire) {
                        $user = null;
                        $content = '';
                        if ($data['source_id'] === 'course_ratings') {
                            $rating = \App\Models\Courses\CourseRating::with('user')->findOrFail($data['comment_id']);
                            $user = $rating->user;
                            $content = $rating->review;
                        } else {
                            $comment = Comment::with('user')->findOrFail($data['comment_id']);
                            $user = $comment->user;
                            $content = $comment->content;
                        }
                        CourseTestimonial::create([
                            'course_id' => $this->ownerRecord->id,
                            'name' => $user?->name ?? 'Anonymous',
                            'job_title' => 'Student',
                            'content' => $content,
                        ]);
                    })
                    ->modalHeading(__('tutor.form.create_testimonial_from_comment'))


            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading(__('tutor.empty.no_testimonials'))
            ->emptyStateDescription(__('tutor.empty.testimonials_appear_here'))
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text');
    }
}
