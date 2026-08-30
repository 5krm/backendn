<?php

namespace App\Livewire\Tutor;

use App\Models\Lessons\LessonComment\Comment;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class CommentRepliesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public int $commentId;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Comment::query()
                    ->where('parent_id', $this->commentId)
                    ->with('user')
                    ->oldest()
            )
            ->columns([
                ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(32)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),

                TextColumn::make('user.name')
                    ->label(__('tutor.comments.student'))
                    ->weight('medium'),

                TextColumn::make('content')
                    ->label(__('tutor.comments.comment'))
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label(__('tutor.comments.posted'))
                    ->since()
                    ->sortable(),
            ])
            ->heading($commentId = $this->commentId ? Comment::find($this->commentId)?->content : __('tutor.unknown'))
            ->toolbarActions([
                Action::make('add_reply')
                    ->label(__('tutor.comments.add_reply'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('primary')
                    ->form([
                        Textarea::make('content')
                            ->label(__('tutor.comments.reply_content'))
                            ->required()
                            ->rows(3),
                    ])
                    ->modalHeading(__('tutor.comments.add_reply'))
                    ->action(function (array $data): void {
                        $parent = Comment::find($this->commentId);
                        Comment::create([
                            'content' => $data['content'],
                            'user_id' => auth()->id(),
                            'lesson_id' => $parent->lesson_id,
                            'parent_id' => $this->commentId,
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(__('tutor.comments.edit'))
                    ->visible(fn (Comment $record) => $record->user_id === auth()->id())
                    ->form([
                        Textarea::make('content')
                            ->label(__('tutor.comments.comment'))
                            ->required()
                            ->rows(2),
                    ]),

                DeleteAction::make(),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_comments'))
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }

    public function render(): View
    {
        return view('livewire.tutor.comment-replies-table');
    }
}
