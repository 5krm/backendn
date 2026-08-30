<?php

namespace App\Filament\Tutor\Resources\Courses\Pages;

use App\Actions\UpdateCoursePrice;
use App\Enums\CourseStatus;
use App\Events\CoursePublished;
use App\Filament\Tutor\Resources\Courses\CourseResource;
use App\Jobs\Stripe\UpdateStripeProduct;
use App\Models\Courses\CoursePrice;
use App\Models\Organization;
use App\Models\OrganizationFollower;
use App\Models\Wishlist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Throwable;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected const PRICING_FIELDS = ['is_free', 'price', 'old_price'];
    protected ?CourseStatus $oldStatus = null;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }
    protected function beforeSave(): void
    {
        // Capture the TRUE original value before the DB write happens
        $this->oldStatus = $this->getRecord()->getOriginal('status');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach (self::PRICING_FIELDS as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    protected function afterSave()
    {
        UpdateStripeProduct::dispatch($this->record);
        /** @var \App\Models\Courses\Course $record */
        $record = $this->getRecord();

        $isPublishedToWaiters = ($this->oldStatus == CourseStatus::preview && $record->status == CourseStatus::published);
        $hasWaiters = Wishlist::where('course_id', $record->id)->exists();
        $hasFollowers = OrganizationFollower::where('organization_id', $this->record->organization_id)->pluck('user_id');

        if ($record->wasChanged('status') && $isPublishedToWaiters && ($hasWaiters || $hasFollowers)) {
            event(new CoursePublished($record));
            // dd([$this->oldStatus, $newStatus, $record->getChanges()]);
        }
    }

    public function savePricing(): void
    {

        $data = $this->form->getState();
        $pricingData = array_intersect_key($data, array_flip(self::PRICING_FIELDS));

        $this->handleRecordUpdate($this->getRecord(), $pricingData);

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();

        (new UpdateCoursePrice())->execute($this->record);
    }

    public function getTitle(): string
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction()->url($this->getResource()::getUrl('index')),
        ];
    }
}
