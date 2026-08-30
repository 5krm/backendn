<?php

namespace App\Filament\Tutor\Resources\Promotions\Pages;

use App\Filament\Tutor\Resources\Promotions\PromotionResource;
use App\Jobs\Stripe\CreateStripePromotion;
use Filament\Resources\Pages\CreateRecord;

class CreatePromotion extends CreateRecord
{
    protected static string $resource = PromotionResource::class;

    protected function afterCreate(): void
    {
        CreateStripePromotion::dispatch($this->record);
    }
}
