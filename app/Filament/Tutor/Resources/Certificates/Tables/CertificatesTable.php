<?php

namespace App\Filament\Tutor\Resources\Certificates\Tables;

use App\Actions\GenerateCertificate;
use App\Models\Certificate;
use App\Models\Courses\Course;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('tutor_id', auth()->user()->id))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('tutor.table.student_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.title')
                    ->label(__('tutor.resources.course'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('certificate_number')
                    ->label(__('tutor.table.certificate_number'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('verification_code')
                    ->label(__('tutor.table.verification_code'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('tutor.table.certificate_status'))
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Certificate::STATUS_VALID => __('tutor.table.status_valid'),
                        Certificate::STATUS_REVOKED => __('tutor.table.status_revoked'),
                        Certificate::STATUS_EXPIRED => __('tutor.table.status_expired'),
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        Certificate::STATUS_VALID => 'success',
                        Certificate::STATUS_REVOKED => 'danger',
                        Certificate::STATUS_EXPIRED => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('score')
                    ->label(__('tutor.table.score'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->label(__('tutor.table.issued_at'))
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label(__('tutor.table.completion_date'))
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_id')
                    ->label(__('tutor.filter.course'))
                    ->options(function () {
                        return Course::where('tutor_id', auth()->user()->id)
                            ->pluck('title', 'id');
                    }),
                SelectFilter::make('status')
                    ->label(__('tutor.table.certificate_status'))
                    ->options([
                        Certificate::STATUS_VALID => __('tutor.table.status_valid'),
                        Certificate::STATUS_REVOKED => __('tutor.table.status_revoked'),
                        Certificate::STATUS_EXPIRED => __('tutor.table.status_expired'),
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('base.download'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->action(function ($record) {
                        $course = $record->load("course")->course;
                        $user = $record->load("user")->user;

                        $pdf = (new GenerateCertificate)->execute($user, $course);

                        return response()->streamDownload(
                            fn () => print($pdf->getContent()),
                            "certificate-{$record->certificate_number}.pdf",
                            ['Content-Type' => 'application/pdf'],
                        );

                    }),
                Action::make('revoke')
                    ->label(__('tutor.table.revoke'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Certificate $record): bool => $record->status === Certificate::STATUS_VALID)
                    ->action(function (Certificate $record): void {
                        $record->update(['status' => Certificate::STATUS_REVOKED]);

                        Notification::make()
                            ->title(__('tutor.table.status_revoked'))
                            ->success()
                            ->send();
                    }),
                Action::make('restoreValidity')
                    ->label(__('tutor.table.restore_validity'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Certificate $record): bool => $record->status === Certificate::STATUS_REVOKED)
                    ->action(function (Certificate $record): void {
                        $record->update(['status' => Certificate::STATUS_VALID]);

                        Notification::make()
                            ->title(__('tutor.table.status_valid'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                // No bulk actions - certificates are auto-generated and cannot be deleted
            ])
            ->defaultSort('created_at', 'desc');
    }
}
