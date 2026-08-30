<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AtRiskStudentsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return __('tutor.widgets.at_risk_students');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;
        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        return $table
            ->heading(__('tutor.widgets.at_risk_students'))
            ->query(
                Enrollment::query()
                    ->with(['user', 'course'])
                    ->whereIn('course_id', $courseIds)
                    ->where('progress', '<', 30)
                    ->whereNull('passed_at')
                    ->where('updated_at', '<', now()->subDays(7))
                    ->orderBy('updated_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(32)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('tutor.tables.student'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label(__('tutor.tables.course'))
                    ->limit(20),
                Tables\Columns\TextColumn::make('progress')
                    ->label(__('tutor.tables.progress'))
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('tutor.tables.last_active'))
                    ->since()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_at_risk_students'))
            ->emptyStateDescription(__('tutor.empty.all_students_engaged'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
