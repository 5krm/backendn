<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
