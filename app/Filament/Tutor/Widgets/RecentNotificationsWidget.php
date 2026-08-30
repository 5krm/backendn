<?php

namespace App\Filament\Tutor\Widgets;

use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Notifications\DatabaseNotification;

class RecentNotificationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return __('tutor.notifications.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DatabaseNotification::query()
                    ->where('notifiable_id', auth()->id())
                    ->where('notifiable_type', 'App\Models\User')
                    ->latest()
                    ->limit(5)
            )
            ->heading(__('tutor.notifications.title'))
            ->poll('30s')
            ->columns([
                Tables\Columns\IconColumn::make('icon')
                    ->label('')
                    ->icon(fn ($record) => $record->data['icon'] ?? 'heroicon-o-bell')
                    ->color(fn ($record) => $record->data['color'] ?? 'gray')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('data.title')
                    ->label(__('tutor.notifications.notification'))
                    ->weight('bold')
                    ->color(fn ($record) => $record->read_at ? 'gray' : 'primary')
                    ->formatStateUsing(function (string $state): string {
                        $titles = [
                            'new_enrollment' => __('notifications.titles.new_enrollment'),
                            'course_completed' => __('notifications.titles.course_completed'),
                            'new_comment' => __('notifications.titles.new_comment'),
                            'certificate_issued' => __('notifications.titles.certificate_issued'),
                        ];

                        return $titles[$state] ?? $state;
                    }),

                Tables\Columns\TextColumn::make('data.message')
                    ->label(__('notifications.message'))
                    ->formatStateUsing(function (string $state, $record) {
                        $messages = [
                            'new_enrollment' => __('notifications.messages.new_enrollment', [
                                'student_name' => $record->data['student_name'] ?? __('notifications.student'),
                                'course_title' => $record->data['course_title'] ?? __('notifications.course'),
                            ]),
                            'course_completed' => __('notifications.messages.course_completed', [
                                'student_name' => $record->data['student_name'] ?? __('notifications.student'),
                                'course_title' => $record->data['course_title'] ?? __('notifications.course'),
                            ]),
                            'new_comment' => __('notifications.messages.new_comment', [
                                'student_name' => $record->data['student_name'] ?? __('notifications.student'),
                                'course_title' => $record->data['course_title'] ?? __('notifications.course'),
                            ]),
                            'certificate_issued' => __('notifications.messages.certificate_issued', [
                                'student_name' => $record->data['student_name'] ?? __('notifications.student'),
                                'course_title' => $record->data['course_title'] ?? __('notifications.course'),
                            ]),
                        ];

                        return $messages[$record->data['title']] ?? $state;
                    })
                    ->searchable()
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('tutor.notifications.time'))
                    ->since()
                    ->sortable(),

                Tables\Columns\IconColumn::make('read_at')
                    ->label(__('notifications.read'))
                    ->getStateUsing(fn ($record) => ! is_null($record->read_at))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->size('md'),
            ])
            ->recordActions([
                Action::make('mark_read')
                    ->label(__('tutor.notifications.mark_read'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => is_null($record->read_at))
                    ->action(function (DatabaseNotification $record) {
                        $record->markAsRead();
                    }),
            ])
            ->emptyStateHeading(__('tutor.notifications.no_notifications'))
            ->emptyStateDescription(__('tutor.notifications.no_recent'))
            ->emptyStateIcon('heroicon-o-bell-slash');
    }
}
