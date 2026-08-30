<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use App\Filament\Tutor\Resources\Courses\RelationManagers\CourseCommentsRelationManager;
use App\Filament\Tutor\Resources\Courses\RelationManagers\PricesRelationManager;
use App\Filament\Tutor\Resources\Courses\RelationManagers\SectionsRelationManager;
use App\Filament\Tutor\Resources\Courses\RelationManagers\TestimonialsRelationManager;
use App\Filament\Tutor\Resources\Courses\RelationManagers\WishlistsRelationManager;
use App\Models\Courses\Course;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use App\Filament\Tutor\Resources\Courses\RelationManagers\EmailsRelationManager;
use App\Filament\Tutor\Resources\Courses\RelationManagers\RatingsRelationManager;
use App\Filament\Tutor\Resources\Courses\Schemas\CourseOrganizationInfoTab;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    protected static function getSharedSaveAction(): array
    {
        return [
            SchemaActions::make([
                Action::make('save_main_info')
                    ->label(__('tutor.save_changes'))
                    ->color('primary')
                    ->action(fn(Action $action) => $action->getLivewire()->save()),
            ])
                ->columnSpanFull(),
        ];
    }

    public static function getComponents(): array
    {
        return [
            Hidden::make('tutor_id')
                ->default(fn() => auth()->user()->id),

            Tabs::make(__('tutor.form.course_setup'))
                ->persistTabInQueryString()
                ->vertical(false)
                ->columnSpanFull()
                ->tabs([
                    Tab::make(__('tutor.form.tab_info'))
                        ->icon('heroicon-o-academic-cap')
                        ->schema(array_merge(CourseInfoForm::schema(), self::getSharedSaveAction())),


                    Tab::make(__('tutor.form.tab_pricing'))
                        ->icon('heroicon-o-currency-dollar')
                        ->schema(CoursePricingForm::schema(true)),
                    Tab::make(__('tutor.form.tab_sections'))
                        ->icon('heroicon-o-rectangle-stack')
                        ->hidden(fn(?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(SectionsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        // This resolves the model correctly before assignment
                                        'ownerRecord' => $record,
                                        // Satisfies the internal Filament requirement for page context
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('sections-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.automated_emails'))
                        ->icon('heroicon-o-envelope')
                        ->hidden(fn (?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(EmailsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        // This resolves the model correctly before assignment
                                        'ownerRecord' => $record,
                                        // Satisfies the internal Filament requirement for page context
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('emails-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.tab_comments'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->hidden(fn(?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(CourseCommentsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('comments-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.tab_testimonials'))
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->hidden(fn(?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(TestimonialsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('testimonials-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.tab_reviews'))
                        ->icon('heroicon-o-star')
                        ->hidden(fn(?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(RatingsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('ratings-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.tab_wishlist'))
                        ->icon('heroicon-o-heart')
                        ->hidden(fn(?Course $record) => $record === null)
                        ->schema([
                            Livewire::make(WishlistsRelationManager::class)
                                ->data(function (?Course $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('wishlist-relation-manager-tab'),
                        ]),
                    Tab::make(__('tutor.form.tab_organization'))
                        ->icon('heroicon-o-building-office')
                        ->hidden(fn (?Course $record) => ! CourseOrganizationInfoTab::isVisible($record))
                        ->schema(CourseOrganizationInfoTab::schema()),
                ]),
        ];
    }
}
