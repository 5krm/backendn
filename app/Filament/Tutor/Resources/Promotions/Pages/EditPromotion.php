<?php

namespace App\Filament\Tutor\Resources\Promotions\Pages;

use App\Filament\Tutor\Resources\Promotions\PromotionResource;
use App\Jobs\Stripe\UpdateStripePromotion;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->visible(fn(): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->hidden(fn(): bool => ! (auth()->user()?->isAdmin() ?? false));
    }

    protected function afterSave(): void
    {
        UpdateStripePromotion::dispatch($this->record);
    }
}
