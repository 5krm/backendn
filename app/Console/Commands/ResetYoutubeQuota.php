<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('app:reset-quota')]
#[Description('Command description')]
class ResetYoutubeQuota extends Command
{
    public function handle()
    {
        Cache::put('youtube_upload_quota', 6, 24 * 60 * 60);
    }
}
