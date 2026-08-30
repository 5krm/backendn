<?php

namespace App\Filament\Tutor\Resources\Certificates\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('tutor_id')
                    ->default(fn () => auth()->user()->id),
                
                Select::make('user_id')
                    ->label('user (student)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Select::make('course_id')
                    ->label('Course')
                    ->relationship('course', 'title', function ($query) {
                        return $query->where('tutor_id', auth()->user()->id);
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title)
                    ->searchable()
                    ->preload()
                    ->required(),
                
                TextInput::make('certificate_number')
                    ->label('certificate number')
                    ->default(fn () => \App\Models\Certificate::generateCertificateNumber())
                    ->required()
                    ->unique(ignoreRecord: true),
                
                DateTimePicker::make('issued_at')
                    ->label('issued at')
                    ->default(now())
                    ->required(),
                
                DateTimePicker::make('completed_at')
                    ->label('completion date')
                    ->default(now())
                    ->required(),
                
                TextInput::make('score')
                    ->label('score')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
                
                Textarea::make('template_data')
                    ->label('template_data')
                    ->helperText(' template_data  ')
                    ->rows(3),
            ]);
    }
}
