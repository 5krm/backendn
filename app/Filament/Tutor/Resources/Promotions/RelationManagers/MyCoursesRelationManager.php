<?php

namespace App\Filament\Tutor\Resources\Promotions\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Tables\CoursesTable;
use App\Models\Courses\Course;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MyCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.promotions.my_courses');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->options(Course::query()->where('tutor_id', auth()->id())->get()->pluck('title', 'id')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('tutor_id', auth()->id()))
            ->headerActions([
                AttachAction::make()
                    ->label(__('tutor.promotions.add_course'))
                    ->modalHeading(__('tutor.promotions.add_course'))
                    ->modalDescription(__('tutor.promotions.select_courses_for_promotion'))
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query->where('tutor_id', auth()->id()))
                    ->tableSelect(CoursesTable::class)
                    ->multiple()
                    ->modalWidth('wide'),
            ])
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('tutor.table.cover'))
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/default-course.png')),

                TextColumn::make('title')
                    ->label(__('tutor.table.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('tutor.table.status'))
                    ->badge(),
                TextColumn::make('price')
                    ->label(__('tutor.table.price'))
                    ->money('USD')
                    ->sortable(),
            ])->recordActions([
                // ...
                DetachAction::make()
                    ->label(__('tutor.promotions.remove_course')),
            ]);
    }
}
