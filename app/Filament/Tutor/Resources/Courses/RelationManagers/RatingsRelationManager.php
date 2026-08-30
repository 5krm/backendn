<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use App\Models\Courses\CourseRating;
use App\Models\Courses\CourseTestimonial;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.resources.ratings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label(__('tutor.resources.ratings.student')),
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->label(__('tutor.resources.ratings.rating')),
                Textarea::make('review')
                    ->columnSpanFull()
                    ->label(__('tutor.resources.ratings.message')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course_id')
            ->columns([

                ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->width('50px')
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),
                TextColumn::make('user.name')
                    ->searchable()
                    ->label(__('tutor.ratings.student')),
                TextColumn::make('rating')
                    ->view('filament.columns.course-rating')
                    ->label(__('tutor.ratings.rating')),
                TextColumn::make('created_at')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->label(__('tutor.table.created_at')),
                TextColumn::make('review')
                    ->wrap()
                    ->limit(100)
                    ->label(__('tutor.ratings.message')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('copy_testimonial')
                    ->label(__('tutor.comments.copy_testimonial'))
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->action(function (CourseRating $record): void {
                        $testimonial = CourseTestimonial::create([
                            'name' => $record->user->name,
                            'content' => $record->review,
                            'course_id' => $record->course_id,
                            'job_title' => '',
                        ]);

                        $profile = $record->user->getMedia('avatars')->last();
                        $isFileExists = $profile ? File::exists($profile->getPath()) : false;

                        if ($profile && $isFileExists) {
                            $testimonial->addMedia($profile->getPath())
                                ->preservingOriginal()
                                ->toMediaCollection('authors');
                        }
                    })->requiresConfirmation(),
            ]);
    }
}
