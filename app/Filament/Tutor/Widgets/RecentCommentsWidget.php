<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Lessons\LessonComment\Comment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCommentsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return __('tutor.widgets.recent_comments');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Comment::query()
                    ->with(['user:id,name', 'lesson:id,title,public_key'])
                    ->whereHas('lesson.course', function ($query) use ($tutorId) {
                        $query->where('tutor_id', $tutorId);
                    })
                    ->latest()
                    ->limit(10)
            )
            ->heading(__('tutor.widgets.recent_comments'))
            ->poll('60s')
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile')
                    ->label(__('tutor.comments.user'))
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('tutor.comments.student'))
                    ->default('Admin')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('lesson.title')
                    ->label(__('tutor.comments.lesson'))
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->lesson->title),

                Tables\Columns\TextColumn::make('content')
                    ->label(__('tutor.comments.comment'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->content)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('tutor.comments.posted'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->color('secondary'),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('tutor.comments.view_lesson'))
                    ->icon('heroicon-o-eye')
                    ->size('sm')
                    ->url(fn (Comment $record): string => route('app.lessons.lesson', ['lesson' => $record->lesson->public_key]))
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->size('sm'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_comments'))
            ->emptyStateDescription(__('tutor.empty.comments_appear_here'))
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
