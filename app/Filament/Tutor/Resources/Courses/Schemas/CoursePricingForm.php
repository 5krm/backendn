<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Section;

class CoursePricingForm
{
    public static function schema(?bool $splitSaveAction = false): array
    {
        return [
            Section::make()
                ->contained(false)
                ->schema([
                    Section::make()
                        ->contained(false)
                        ->schema([
                            Toggle::make('is_free')
                                ->label(__('tutor.form.free_course'))
                                ->helperText(__('tutor.form.free_course_help'))
                                ->reactive()
                                ->inline(false)
                                ->columnSpanFull(),

                            TextInput::make('price')
                                ->label(__('tutor.form.price'))
                                ->numeric()
                                ->minValue(0)
                                ->required(fn($get) => !$get('is_free'))
                                ->prefix('$')
                                ->placeholder('49')
                                ->prefixIcon('heroicon-o-currency-dollar')
                                ->visible(fn($get) => !$get('is_free'))
                                ->default(0)
                                ->columnSpanFull(),

                            TextInput::make('old_price')
                                ->label(__('tutor.form.original_price'))
                                ->numeric()
                                ->minValue(0)
                                ->prefix('$')
                                ->placeholder('99')
                                ->prefixIcon('heroicon-o-receipt-percent')
                                ->helperText(__('tutor.form.show_discount'))
                                ->nullable()
                                ->visible(fn($get) => !$get('is_free'))
                                ->minValue(fn($get)=>  $get('price'))
                                ->columnSpanFull(),
                        ])
                        ->columns(1),

                    $splitSaveAction ? SchemaActions::make([
                        Action::make('save_pricing')
                            ->label(__('tutor.save_changes'))
                            ->color('primary')
                            ->extraAttributes([
                                'type' => 'button',
                                'wire:click.prevent' => 'savePricing',
                            ]),
                    ])
                        ->columnSpanFull() : null,
                ])
                ->columns(2),
        ];
    }
}
