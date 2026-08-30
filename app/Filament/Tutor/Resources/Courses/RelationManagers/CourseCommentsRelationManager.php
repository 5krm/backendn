<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use App\Events\NewReplyPosted;
use App\Models\Courses\CourseTestimonial;
use App\Models\Lessons\LessonComment\Comment;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CourseCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.resources.comments');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->whereNull('parent_id')
                    ->with(['user', 'lesson', 'children.user'])
            )
            ->columns([
                ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),

                TextColumn::make('user.name')
                    ->label(__('tutor.comments.student'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('lesson.title')
                    ->label(__('tutor.comments.lesson'))
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn(Comment $record) => $record->lesson?->title),

                TextColumn::make('content')
                    ->label(__('tutor.comments.comment'))
                    ->limit(60)
                    ->tooltip(fn(Comment $record) => $record->content)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('children_count')
                    ->label(__('tutor.comments.replies'))
                    ->counts('children')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label(__('tutor.comments.posted'))
                    ->since()
                    ->sortable()
                    ->color('secondary'),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('reply')
                        ->modalHeading(__('tutor.comments.add_reply'))
                        ->label(__('tutor.comments.reply'))
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->form([
                            Textarea::make('content')
                                ->label(__('tutor.comments.reply_content'))
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Comment $record, array $data): void {
                            $reply = Comment::create([
                                'content'   => $data['content'],
                                'user_id'   => auth()->id(),
                                'lesson_id' => $record->lesson_id,
                                'parent_id' => $record->id,
                            ]);
                            event(new NewReplyPosted($reply));
                        }),

                    Action::make('manage_replies')
                        ->label(fn(Comment $record) => __('tutor.comments.manage_replies') . ' (' . ($record->children_count ?? 0) . ')')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->visible(fn(Comment $record) => ($record->children_count ?? 0) > 0)
                        ->modalHeading(__('tutor.comments.manage_replies'))
                        ->modalContent(fn(Comment $record) => view('livewire.tutor.comment-replies-modal', ['commentId' => $record->id]))
                        ->modalSubmitAction(false)
                        ->slideOver(),

                    Action::make('copy_testimonial')
                        ->label(__('tutor.comments.copy_testimonial'))
                        ->icon('heroicon-o-chat-bubble-left-ellipsis')
                        ->action(function (Comment $record): void {
                            $testimonial =  CourseTestimonial::create([
                                "name" => $record->user->name,
                                "content" => $record->content,
                                "course_id" => $record->lesson->course_id,
                                "job_title" => ""
                            ]);
                            
                            $profile = $record->user->getMedia('avatars')->last();
                            $isFileExists = $profile ? File::exists($profile->getPath()) : false;
                            
                            if ($profile && $isFileExists) {
                                $testimonial->addMedia($profile->getPath())
                                    ->preservingOriginal()
                                    ->toMediaCollection('authors');
                            }
                        })->requiresConfirmation(),

                    DeleteAction::make(),
                ])->color('grey'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('tutor.empty.no_comments'))
            ->emptyStateDescription(__('tutor.empty.comments_appear_here'))
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
