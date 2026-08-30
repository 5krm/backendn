<?php

namespace App\Console\Commands\Backup;

use App\Actions\Backup\BackupRun;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:backup:generate')]
#[Description('Generate a backup of the database')]
class GenerateBackup extends Command
{
    public function handle(): void
    {
        (new BackupRun)->execute();
    }
}
