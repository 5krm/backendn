<?php

namespace App\Filament\Tutor\Resources\Organizations\Schemas;

use App\Filament\Tutor\Resources\Organizations\RelationManagers\CoursesRelationManager;
use App\Filament\Tutor\Resources\Organizations\RelationManagers\FollowersRelationManager;
use App\Filament\Tutor\Resources\Organizations\RelationManagers\UsersRelationManager;
use App\Models\Organization;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Livewire;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }


    public static function getComponents(): array
    {
        return [
            Tabs::make('Organization')
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(__('tutor.form.organization_information'))
                        ->schema([
                            ...OrganizationInfoForm::schema(),
                        ]),

                    Tab::make(__('tutor.form.tab_users'))
                        ->hidden(fn (?Organization $record) => $record === null)
                        ->schema([
                              Livewire::make(UsersRelationManager::class)
                                ->data(function (?Organization $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('users-relation-manager-tab'),
                        ]),

                    Tab::make(__('tutor.form.tab_followers'))
                        ->hidden(fn (?Organization $record) => $record === null)
                        ->schema([
                            Livewire::make(FollowersRelationManager::class)
                                ->data(function (?Organization $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('followers-relation-manager-tab'),
                        ]),

                    Tab::make(__('tutor.form.tab_courses'))
                        ->hidden(fn (?Organization $record) => $record === null)
                         ->schema([
                              Livewire::make(CoursesRelationManager::class)
                                ->data(function (?Organization $record, $livewire) {
                                    return [
                                        'ownerRecord' => $record,
                                        'pageClass' => get_class($livewire),
                                    ];
                                })
                                ->key('courses-relation-manager-tab'),
                        ]),

                ])->columnSpanFull(),
        ];
    }
}
