<?php

namespace App\Filament\Tutor\Resources\Promotions\Schemas;

use App\Enums\PromotionDisplayType;
use App\Models\Promotion;
use App\Support\PromotionTemplateRegistry;
use App\ViewModels\Promotions\PromotionBannerView;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('tutor.promotions.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        TextInput::make('discount_percent')
                            ->label(__('tutor.promotions.discount_percent'))
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->numeric()
                            ->mask('999')
                            ->prefixIcon(Heroicon::PercentBadge)
                            ->disabledOn('edit')
                            ->live(onBlur: true),
                        Textarea::make('description')
                            ->label(__('tutor.promotions.description'))
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->live(onBlur: true),
                        DateTimePicker::make('start')
                            ->label(__('tutor.promotions.start'))
                            ->required()
                            ->minDate(Carbon::now())
                            ->toUserTimezone()
                            ->live(onBlur: true),
                        DateTimePicker::make('end')
                            ->label(__('tutor.promotions.end'))
                            ->required()
                            ->toUserTimezone()
                            ->live(onBlur: true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make(__('tutor.promotions.banner_section'))
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false))
                    ->schema([
                        ToggleButtons::make('display_type')
                            ->label(__('tutor.promotions.display_type'))
                            ->options(collect(PromotionDisplayType::cases())
                                ->mapWithKeys(fn (PromotionDisplayType $type) => [
                                    $type->value => $type->label(),
                                ])
                                ->all())
                            ->default(PromotionDisplayType::Template->value)
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('template')
                            ->label(__('tutor.promotions.template'))
                            ->options(PromotionTemplateRegistry::options())
                            ->default(PromotionTemplateRegistry::defaultTemplate())
                            ->required(fn ($get) => $get('display_type') === PromotionDisplayType::Template->value)
                            ->visible(fn ($get) => $get('display_type') === PromotionDisplayType::Template->value)
                            ->live(),

                        FileUpload::make('banner_image')
                            ->label(__('tutor.promotions.banner_image'))
                            ->helperText(__('tutor.promotions.banner_image_help'))
                            ->image()
                            ->disk('public')
                            ->directory('promotions/banners')
                            ->imageEditor()
                            ->imageCropAspectRatio('4:1')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->required(fn ($get) => $get('display_type') === PromotionDisplayType::Image->value)
                            ->visible(fn ($get) => $get('display_type') === PromotionDisplayType::Image->value)
                            ->live()
                            ->columnSpanFull(),

                        ViewField::make('banner_preview')
                            ->label(__('tutor.promotions.preview'))
                            ->view('filament.tutor.promotions.banner-preview')
                            ->viewData(function ($get, $record) {
                                $displayTypeRaw = $get('display_type') ?? PromotionDisplayType::Template->value;
                                $displayType = $displayTypeRaw instanceof PromotionDisplayType
                                    ? $displayTypeRaw
                                    : (PromotionDisplayType::tryFrom((string) $displayTypeRaw) ?? PromotionDisplayType::Template);

                                return [
                                    'banner' => self::previewBanner($get, $record),
                                    'bannerImagePreviewUrl' => self::resolveBannerImagePreviewUrl($get('banner_image'), $record),
                                    'displayType' => $displayType,
                                ];
                            })
                            ->dehydrated(false)
                            ->live()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }

    protected static function previewBanner(callable $get, ?Promotion $record): ?PromotionBannerView
    {
        $promotion = $record?->replicate() ?? new Promotion;

        if (! $promotion->exists) {
            $promotion->id = 0;
        }

        $displayTypeRaw = $get('display_type') ?? PromotionDisplayType::Template->value;
        $displayType = $displayTypeRaw instanceof PromotionDisplayType
            ? $displayTypeRaw
            : (PromotionDisplayType::tryFrom((string) $displayTypeRaw) ?? PromotionDisplayType::Template);

        $bannerImage = $displayType === PromotionDisplayType::Image
            ? self::resolveBannerImagePreviewUrl($get('banner_image'), $record)
            : self::resolveBannerImageForPreview($get('banner_image'), $record);

        $promotion->forceFill([
            'title' => $get('title') ?: __('tutor.promotions.preview_title'),
            'description' => $get('description'),
            'discount_percent' => (int) ($get('discount_percent') ?: 50),
            'start' => $get('start') ?? now(),
            'end' => $get('end') ?? now()->addDays(30),
            'display_type' => $displayType,
            'template' => $get('template') ?: PromotionTemplateRegistry::defaultTemplate(),
            'banner_image' => $bannerImage,
        ]);

        $promotion->setRelation('courses', collect());

        return new PromotionBannerView($promotion);
    }

    protected static function resolveBannerImageForPreview(mixed $state, ?Promotion $record): ?string
    {
        $file = self::extractBannerImageFile($state);

        if ($file instanceof TemporaryUploadedFile) {
            return $file->temporaryUrl();
        }

        if (is_string($file) && filled($file)) {
            return $file;
        }

        return $record?->banner_image;
    }

    protected static function resolveBannerImagePreviewUrl(mixed $state, ?Promotion $record): ?string
    {
        $file = self::extractBannerImageFile($state);

        if ($file instanceof TemporaryUploadedFile) {
            $url = $file->temporaryUrl();

            return str_starts_with($url, 'http') ? $url : url($url);
        }

        if (is_string($file) && filled($file)) {
            if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
                return $file;
            }

            if (str_starts_with($file, '/')) {
                return url($file);
            }

            return Storage::disk('public')->url($file);
        }

        return $record?->banner_image_url;
    }

    protected static function extractBannerImageFile(mixed $state): mixed
    {
        if (is_array($state)) {
            return collect($state)
                ->flatten()
                ->first(fn ($item) => filled($item));
        }

        if (filled($state)) {
            return $state;
        }

        return null;
    }
}
