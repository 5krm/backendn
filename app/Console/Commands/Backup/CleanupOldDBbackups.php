<?php

namespace App\Console\Commands\Backup;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;

#[Signature('app:backup:cleanup')]
#[Description('Cleans up old database backups')]
class CleanupOldDBbackups extends Command
{
    private const int DAYS = 3;

    public function handle(): void
    {
        $disk = Storage::disk('azure-backup');
        $files = $disk->files('backups');
        if (count($files) <= 1) {
            return;
        }

        $cutoff = Carbon::now()->subDays(self::DAYS)->timestamp;
        $filesToRemove = $this->listFilesToRemove($files, $cutoff);
        if (count($filesToRemove) <= 0) {
            return;
        }

        // We should make sure that the most recent backup is not deleted.
        if (count($filesToRemove) == count($files)) {
            array_pop($filesToRemove);
        }

        foreach ($filesToRemove as $timestamp => $file) {
            if ($timestamp <= $cutoff) {
                $disk->delete($file);
            }
        }
    }

    private function listFilesToRemove(array $files, int $cutoffTS): array
    {
        $result = [];
        foreach ($files as $file) {
            $filename = explode('.', basename($file))[0];
            $nameParts = explode('_', $filename);
            $fileTimestamp = $nameParts[count($nameParts) - 1];
            if (! $this->isValidTimestamp($fileTimestamp)) {
                continue;
            }

            $fileTimestamp = (int)$fileTimestamp;
            if ($fileTimestamp <= $cutoffTS) {
                $result[$fileTimestamp] = $file;
            }
        }

        // oldest timestamps first, newest at the end
        ksort($result);
        return $result;
    }

    private function isValidTimestamp(string $timestamp): bool
    {
        if (!ctype_digit($timestamp)) {
            return false;
        }

        $timestamp = (int)$timestamp;
        return ($timestamp >= 1_000_000_000 && $timestamp <= 2147483647);
    }
}
