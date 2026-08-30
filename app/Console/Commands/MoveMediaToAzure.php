<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Signature('app:move-media-to-azure')]
#[Description('Command description')]
class MoveMediaToAzure extends Command
{
    public function handle()
    {
        $medias = Media::query()
            ->where('disk', '!=', 'azure')
            ->whereHas('model')
            ->with('model')
            ->get()
            ->filter(function (Media $media) {
                return Storage::disk($media->disk)->exists($media->getPathRelativeToRoot());
            })
            ->values();

        if ($medias->isEmpty()) {
            $this->info('No media found to move.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($medias->count());
        $bar->start();

        foreach ($medias as $media) {
            $model = $media->model;
            $media->copy($model, $media->collection_name, 'azure');
            $media->delete();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return self::SUCCESS;
    }
}
