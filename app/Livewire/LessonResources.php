<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lessons\LessonResource;
use Illuminate\Support\Facades\Route;

class LessonResources extends Component
{
    public $resources = [];

    public function mount()
    {
        $lessonKey = Route::current()->parameter('lesson');
        $this->resources = LessonResource::with('media')
            ->whereHas('lesson', fn($q) => $q->where('public_key', $lessonKey))
            ->get();
    }

    public function render()
    {
        return view('livewire.lesson-resources');
    }

    public function download($id)
    {
        $resource = LessonResource::findOrFail($id);
        return response()->download(
            storage_path('app/public/' . $resource->file_path),
            $resource->title . '.' . pathinfo($resource->file_path, PATHINFO_EXTENSION)
        );
    }
}
