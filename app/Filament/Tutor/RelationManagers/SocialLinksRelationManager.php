<?php

namespace App\Filament\Tutor\RelationManagers;

use App\Enums\SocialPlatform;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class SocialLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'socialLinks';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('profile.social_links.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform')
                    ->label(__('profile.social_links.platform'))
                    ->options(collect(SocialPlatform::cases())->mapWithKeys(
                        fn (SocialPlatform $platform) => [$platform->value => $platform->getLabel()]
                    ))
                    ->required()
                    ->native(false)
                    ->searchable(false)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('user_id', $this->getOwnerRecord()->getKey())
                    )
                    ->prefixIcon(fn ($get) => SocialPlatform::tryFrom((string) $get('platform'))?->getIcon() ?? 'heroicon-o-link')
                    ->live(),
                TextInput::make('url')
                    ->label(__('profile.social_links.url'))
                    ->url()
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon(fn ($get) => SocialPlatform::tryFrom((string) $get('platform'))?->getIcon() ?? 'heroicon-o-link')
                    ->placeholder('https://…'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                IconColumn::make('platform_icon')
                    ->label('')
                    ->getStateUsing(fn ($record) => true)
                    ->icon(function ($record) {
                        $platform = $record->platform instanceof SocialPlatform
                            ? $record->platform
                            : SocialPlatform::tryFrom((string) ($record->getAttributes()['platform'] ?? ''));

                        return $platform?->getIcon() ?? 'heroicon-o-link';
                    })
                    ->alignCenter(),
                TextColumn::make('platform')
                    ->label(__('profile.social_links.platform'))
                    ->formatStateUsing(function ($state) {
                        if ($state instanceof SocialPlatform) {
                            return $state->getLabel();
                        }

                        return SocialPlatform::tryFrom((string) $state)?->getLabel() ?? (string) $state;
                    })
                    ->sortable(),
                TextColumn::make('url')
                    ->label(__('profile.social_links.url'))
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->limit(50)
                    ->searchable()
                    ->icon('heroicon-m-arrow-top-right-on-square'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->emptyStateHeading(__('profile.social_links.title'))
            ->emptyStateDescription(__('profile.social_links.subtitle'))
            ->emptyStateIcon('heroicon-o-share');
    }
}
