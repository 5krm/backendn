<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Pages;

use App\Events\CourseLessonsUpdatedEvent;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLessons extends ListRecords
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        // return [
        //     // This is required to make the "Create" button appear on the new page
        //     CreateAction::make(),
        // ];
        return [
            CreateAction::make()
                ->modalHeading(__('tutor.quick_actions.create_lesson'))
                ->modalSubmitActionLabel(__('tutor.form.add_lesson'))
                ->modalWidth('4xl')
                ->icon('heroicon-o-plus')
                ->fillForm(function () {
                    $parent = $this->getParentRecord();

                    return [
                        'section_id' => $parent?->id,
                        'course_id' => $parent?->course_id,
                    ];
                })
                ->mutateFormDataUsing(function (array $data) {
                    $parent = $this->getParentRecord();
                    $data['section_id'] ??= $parent?->id;
                    $data['course_id'] ??= $parent?->course_id;

                    return $data;
                })->after(function ($record) {
                    event(new CourseLessonsUpdatedEvent($this->getParentRecord()->course_id));
                }),
        ];
    }
}
