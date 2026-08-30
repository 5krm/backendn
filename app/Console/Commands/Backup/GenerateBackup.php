<?php

namespace App\Console\Commands\Backup;

use App\Actions\Backup\BackupRun;
use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;

#[Signature('app:backup:generate')]
#[Description('Generate a backup of the database')]
class GenerateBackup extends Command
{
    public function handle(): void
    {
        (new BackupRun())->execute();
    }
}
