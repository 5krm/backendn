<?php

namespace App\Filament\Tutor\Resources\Courses\Schemas;

use App\Models\Courses\Course;
use App\Models\Organization;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class CourseOrganizationInfoTab
{
    public static function schema(): array
    {
        return [
            Section::make(__('tutor.form.organization_information'))
                ->description(__('tutor.form.organization_info_desc'))
                ->schema([
                    Placeholder::make('organization_logo')
                        ->label(__('tutor.form.organization_logo'))
                        ->content(function (?Course $record): HtmlString|string {
                            $organization = self::organization($record);

                            if (! $organization) {
                                return '-';
                            }

                            return new HtmlString(
                                '<img src="' . e($organization->logo_url) . '" width="100" height="100"  alt="" class="w-24 h-24 object-cover rounded-lg">'
                            );
                        })
                        ->columnSpanFull(),

                    Placeholder::make('organization_name')
                        ->label(__('tutor.form.organization_name'))
                        ->content(fn (?Course $record) => self::organization($record)?->name ?? '-'),

                    Placeholder::make('organization_slug')
                        ->label(__('tutor.form.slug'))
                        ->content(fn (?Course $record) => self::organization($record)?->slug ?? '-'),

                    Placeholder::make('organization_description')
                        ->label(__('tutor.form.description'))
                        ->content(fn (?Course $record) => self::organization($record)?->description ?: '-')
                        ->columnSpanFull(),

                    Placeholder::make('organization_is_active')
                        ->label(__('tutor.form.active'))
                        ->content(function (?Course $record): string {
                            $organization = self::organization($record);

                            if (! $organization) {
                                return '-';
                            }

                            return $organization->is_active
                                ? __('tutor.form.yes')
                                : __('tutor.form.no');
                        }),
                ])
                ->columns(2),

            Section::make(__('tutor.form.organization_public_profile'))
                ->description(__('tutor.form.organization_public_profile_desc'))
                ->schema([
                    Placeholder::make('organization_website')
                        ->label(__('tutor.form.website'))
                        ->content(fn (?Course $record) => self::organization($record)?->website ?: '-'),

                    Placeholder::make('organization_category')
                        ->label(__('tutor.form.category'))
                        ->content(fn (?Course $record) => self::organization($record)?->category ?: '-'),

                    Placeholder::make('organization_founded')
                        ->label(__('tutor.form.founded'))
                        ->content(fn (?Course $record) => self::organization($record)?->founded ?: '-'),

                    Placeholder::make('organization_position')
                        ->label(__('tutor.form.position'))
                        ->content(fn (?Course $record) => self::organization($record)?->position ?: '-'),
                ])
                ->columns(2),
        ];
    }

    public static function isVisible(?Course $record): bool
    {
        if ($record === null || blank($record->organization_id)) {
            return false;
        }

        return auth()->user()?->isAdmin() ?? false;
    }

    protected static function organization(?Course $record): ?Organization
    {
        return $record?->organization;
    }
}
