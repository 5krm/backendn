<?php

namespace App\Filament\Tutor\Resources;

use App\Filament\Tutor\Resources\NotificationResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.notifications');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.notification');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.notifications');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->whereNull('read_at')
            ->where('notifiable_id', auth()->id())
            ->where('notifiable_type', 'App\Models\User')
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('title')
                            ->label(__('notifications.title'))
                            ->content(fn ($record) => $record->data['title'] ?? 'Notification'),

                        Forms\Components\Placeholder::make('message')
                            ->label(__('notifications.message'))
                            ->content(fn ($record) => $record->data['message'] ?? ''),

                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('notifications.received_at'))
                            ->content(fn ($record) => $record->created_at->diffForHumans()),

                        Forms\Components\Placeholder::make('read_at')
                            ->label(__('notifications.status'))
                            ->content(fn ($record) => $record->read_at ? 'Read at '.$record->read_at->diffForHumans() : 'Unread'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('icon')
                    ->label('')
                    ->icon(fn ($record) => $record->data['icon'] ?? 'heroicon-o-bell')
                    ->color(fn ($record) => $record->data['color'] ?? 'gray')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('data.title')
                    ->label(__('notifications.type'))
                    ->searchable()
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
                                'student_name' => $record->data['student_name'] ?? 'A student',
                                'course_title' => $record->data['course_title'] ?? 'a course',
                            ]),
                            'course_completed' => __('notifications.messages.course_completed', [
                                'student_name' => $record->data['student_name'] ?? 'A student',
                                'course_title' => $record->data['course_title'] ?? 'a course',
                            ]),
                            'new_comment' => __('notifications.messages.new_comment', [
                                'student_name' => $record->data['student_name'] ?? 'A student',
                                'course_title' => $record->data['course_title'] ?? 'a course',
                            ]),
                            'certificate_issued' => __('notifications.messages.certificate_issued', [
                                'student_name' => $record->data['student_name'] ?? 'A student',
                                'course_title' => $record->data['course_title'] ?? 'a course',
                            ]),
                        ];

                        return $messages[$record->data['title']] ?? $state;
                    })
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('notifications.received_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\IconColumn::make('read_at')
                    ->label(__('notifications.read'))
                    ->getStateUsing(fn ($record) => ! is_null($record->read_at))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->size('md')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label(__('notifications.unread_only'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('read_at'))
                    ->toggle(),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('tutor.type'))
                    ->options([
                        'App\Notifications\NewEnrollmentNotification' => __('notifications.types.new_enrollment'),
                        'App\Notifications\CourseCompletedNotification' => __('notifications.types.course_completed'),
                        'App\Notifications\NewCommentNotification' => __('notifications.types.new_comment'),
                        'App\Notifications\CertificateIssuedNotification' => __('notifications.types.certificate_issued'),
                    ]),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label(__('notifications.mark_as_read'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->read_at)
                    ->action(function ($record) {
                        $record->markAsRead();
                        FilamentNotification::make()
                            ->title(__('notifications.mark_as_read'))
                            ->success()
                            ->send();
                    }),

                Action::make('markAsUnread')
                    ->label(__('notifications.mark_as_unread'))
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn ($record) => $record->read_at)
                    ->action(function ($record) {
                        $record->markAsUnread();
                        FilamentNotification::make()
                            ->title(__('notifications.mark_as_unread'))
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label(__('notifications.mark_as_read'))
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->markAsRead();
                            FilamentNotification::make()
                                ->title(__('notifications.mark_as_read'))
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('notifications.empty.heading'))
            ->emptyStateDescription(__('notifications.empty.description'))
            ->emptyStateIcon('heroicon-o-bell-slash');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('notifiable_id', auth()->id())
            ->where('notifiable_type', 'App\Models\User');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
        ];
    }
}
