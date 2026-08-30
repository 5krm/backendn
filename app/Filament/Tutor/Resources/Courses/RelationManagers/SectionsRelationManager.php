<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use App\Models\Courses\Course;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema; // Import Schema

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    public function form(Schema $schema): Schema
    {
        return SectionResource::form($schema);
    }

    public function table(Table $table): Table
    {

        return SectionResource::table($table);
    }
}
