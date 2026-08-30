<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use App\Filament\Tutor\Resources\Courses\Schemas\CourseInfoForm;
use App\Filament\Tutor\Resources\Courses\Schemas\CoursePricingForm;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;

class CourseWizard
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make(__('tutor.form.tab_info'))
                    ->icon('heroicon-o-academic-cap')
                    ->schema(CourseInfoForm::schema()),
                Wizard\Step::make(__('tutor.form.tab_pricing'))
                    ->icon('heroicon-o-currency-dollar')
                    ->schema(CoursePricingForm::schema()),
            ])
                ->columnSpanFull(),
            Hidden::make('tutor_id')
                ->default(fn() => auth()->user()->id),
        ];
    }
}
