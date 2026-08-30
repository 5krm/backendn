<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Schemas;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\RelationManagers\LessonsRelationManager;
use Filament\Schemas\Schema;
use App\Models\Courses\Course;
use App\Models\Courses\CourseSection;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(
                        __('tutor.form.section_title')
                    )
                    ->maxLength(190)
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label(__('tutor.form.section_description'))
                    ->rows(3)
                    ->columnSpanFull(),


            ]);
    }
}
