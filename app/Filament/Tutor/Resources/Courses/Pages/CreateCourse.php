<?php

namespace App\Filament\Tutor\Resources\Courses\Pages;

use App\Enums\CourseStatus;
use App\Events\CoursePublished;
use App\Filament\Tutor\Resources\Courses\CourseResource;
use App\Jobs\Stripe\CreateStripeProduct;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('tutor.notifications.course_created'))
            ->body(__('tutor.notifications.course_created_body'))
            ->duration(5000);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate required fields and show custom notifications
        $this->validateCourseData($data);

        // Set tutor_id
        $data['tutor_id'] = auth()->user()->id;

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }

    protected function validateCourseData(array $data): void
    {
        $warnings = [];

        // Check if title is provided
        if (empty($data['title'])) {
            Notification::make()
                ->danger()
                ->title(__('tutor.validation.title_required'))
                ->body(__('tutor.validation.title_required_body'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Check if description is provided
        if (empty($data['description'])) {
            Notification::make()
                ->danger()
                ->title(__('tutor.validation.description_required'))
                ->body(__('tutor.validation.description_required_body'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Check if language is selected
        if (empty($data['lang'])) {
            Notification::make()
                ->danger()
                ->title(__('tutor.validation.language_required'))
                ->body(__('tutor.validation.language_required_body'))
                ->persistent()
                ->send();

            $this->halt();
        }

        // Warning: No category selected
        if (empty($data['category_id'])) {
            $warnings[] = __('tutor.validation.no_category');
        }

        // Warning: No objectives provided
        if (empty($data['objectives'])) {
            $warnings[] = __('tutor.validation.no_objectives');
        }

        // Check pricing
        if (! isset($data['is_free']) || $data['is_free'] === false) {
            if (empty($data['price']) || $data['price'] <= 0) {
                Notification::make()
                    ->danger()
                    ->title(__('tutor.validation.price_required'))
                    ->body(__('tutor.validation.price_required_body'))
                    ->persistent()
                    ->send();

                $this->halt();
            }

            // Check if price is negative
            if ($data['price'] < 0) {
                Notification::make()
                    ->danger()
                    ->title(__('tutor.validation.price_negative'))
                    ->body(__('tutor.validation.price_negative_body'))
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        // Show all warnings in one notification
        if (! empty($warnings)) {
            Notification::make()
                ->warning()
                ->title(__('tutor.validation.missing_optional_fields'))
                ->body(implode('<br>', $warnings))
                ->persistent()
                ->send();
        }
    }

    protected function afterCreate(): void
    {

        CreateStripeProduct::dispatch($this->record);
        if ($this->record->status == CourseStatus::published) {
            event(new CoursePublished($this->record));
        }
        // Show success notification with next steps
        Notification::make()
            ->info()
            ->title(__('tutor.notifications.next_steps'))
            ->body(__('tutor.notifications.next_steps_body'))
            ->duration(10000)
            ->send();
    }
}
