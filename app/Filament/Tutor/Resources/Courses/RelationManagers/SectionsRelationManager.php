<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table; // Import Schema

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
