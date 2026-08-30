<?php

namespace App\Filament\Tutor\Resources\Students\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile')
                    ->label(__('tutor.table.cover'))
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/default-course.png')),
                TextColumn::make('name')
                    ->label(__('tutor.students.student'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('tutor.students.email'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('email_verified_at')
                    ->label(__('tutor.students.email_verified'))
                    ->getStateUsing(fn($record) => !is_null($record->email_verified_at))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('tutor.students.phone'))
                    ->searchable()
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->extraAttributes([
                        'style' => 'direction: ltr;',
                    ]),
                TextColumn::make('country.name')
                    ->label(__('tutor.students.country'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('courses_count')
                    ->label(__('tutor.resources.courses'))
                    ->formatStateUsing(fn($record) => __('tutor.students.courses_no', ['count' => $record->courses_count]))
                    ->action(
                        Action::make('viewCourses')
                            ->modalHeading(fn($record) => __('tutor.students.courses_for', ['student' => $record->name]))
                            ->modalContent(fn($record) => view('filament.tables.modals.student-courses', ['courses' => $record->courses]))
                            ->modalCancelActionLabel(__('base.cancel'))
                    ),
            ])
            ->filters([
                SelectFilter::make('country')
                    ->label(__('tutor.students.country'))
                    ->relationship('country', 'name'),
                SelectFilter::make('course')
                    ->label(__('tutor.filter.course'))
                    ->relationship('courses', 'title', function (Builder $query) {
                        $user = auth()->user();
                        if (!$user->isAdmin()) {
                            $query->where('tutor_id', $user->tutorProfile?->id);
                        }
                    })
                    ->preload()
                    ->searchable(),
                Filter::make('courses_count')
                    ->schema([
                        TextInput::make('courses_min')
                            ->label(__('tutor.students.courses_min'))
                            ->numeric()
                            ->placeholder('Min'),
                        TextInput::make('courses_max')
                            ->label(__('tutor.students.courses_max'))
                            ->numeric()
                            ->placeholder('Max'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['courses_min'] !== null && $data['courses_min'] !== '',
                                fn(Builder $query): Builder => $query->has('courses', '>=', intval($data['courses_min'])),
                            )
                            ->when(
                                $data['courses_max'] !== null && $data['courses_max'] !== '',
                                fn(Builder $query): Builder => $query->has('courses', '<=', intval($data['courses_max'])),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['courses_min'] !== null && $data['courses_min'] !== '') {
                            $indicators[] = __('tutor.students.courses_min_indicator', ['count' => $data['courses_min']]);
                        }
                        if ($data['courses_max'] !== null && $data['courses_max'] !== '') {
                            $indicators[] = __('tutor.students.courses_max_indicator', ['count' => $data['courses_max']]);
                        }
                        return $indicators;
                    }),
                TernaryFilter::make('email_verified_at')
                    ->label(__('tutor.students.email_verified'))
                    ->nullable()
                    ->placeholder(__('tutor.students.email_verified_placeholder'))
                    ->trueLabel(__('tutor.students.verified'))
                    ->falseLabel(__('tutor.students.unverified')),
                TrashedFilter::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                \Filament\Actions\Action::make('resend_verification')
                    ->label(__('tutor.students.resend_verification'))
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->visible(fn($record): bool => is_null($record->email_verified_at))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->email)->send(new \App\Mail\VerifyEmail($record));
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title(__('tutor.students.verification_sent'))
                                ->send();
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Resend student verification email error: ' . $e->getMessage());
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title(__('tutor.students.verification_failed'))
                                ->send();
                        }
                    }),
            ])

            ->toolbarActions([
                ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable()
                ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('tutor.empty.no_data'))
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->defaultSort('created_at', 'desc');
    }
}
