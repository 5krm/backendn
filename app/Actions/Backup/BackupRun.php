<?php

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupRun
{
    private int $timeout = 1200;
    private string $outputFilename;
    private string $outputFilepath;
    private string $credentialsFile;

    public function __construct()
    {
        $this->outputFilename = 'db_backup_' . now()->timestamp . '.sql.gz';
        $this->credentialsFile = storage_path('app/.my-' . now()->format('Y-m-d_H-i-s') . '.cnf');
        $this->outputFilepath = storage_path('app/backups/' . $this->outputFilename);
    }

    public function execute(): bool
    {
        $deleteLocalBackup = false;

        try {
            $this->setupBackupDirectory();
            $this->createCredentialsFile();

            $command = $this->buildDumpCommand($this->credentialsFile, $this->outputFilepath);
            $process = Process::fromShellCommandline(
                command: sprintf('bash -o pipefail -c %s', escapeshellarg($command)),
                cwd: null,
                env: null,
                input: null,
                timeout: $this->timeout
            );

            $process->run();

            if (! $process->isSuccessful()) {
                throw new \RuntimeException(
                    message: $process->getErrorOutput() ?: $process->getOutput(),
                    code: $process->getExitCode()
                );
            }

            $this->uploadBackupToAzure($this->outputFilepath, 'backups/' . $this->outputFilename);
            $deleteLocalBackup = true;

            return true;
        } catch (\Throwable $e) {
            Log::error('Database backup process failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return false;
        } finally {
            if (file_exists($this->credentialsFile)) {
                unlink($this->credentialsFile);
            }

            if ($deleteLocalBackup && file_exists($this->outputFilepath)) {
                unlink($this->outputFilepath);
            }
        }
    }

    private function uploadBackupToAzure(string $fromPath, string $toPath): void
    {
        $stream = fopen($fromPath, 'r');
        try {
            if (! $stream) {
                throw new \RuntimeException('Failed to open backup file');
            }

            Storage::disk('azure-backup')->writeStream($toPath, $stream);
        } finally {
            fclose($stream);
        }
    }

    private function buildDumpCommand(string $credentialsFile, string $backupFilepath): string
    {
        $database = config('database.connections.mysql.database');

        $ignoreTables = [
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
        ];

        $ignoreOptions = collect($ignoreTables)
            ->map(fn($table) => "--ignore-table={$database}.{$table}")
            ->implode(' ');

        $command = sprintf(
            'mysqldump --defaults-extra-file=%s --skip-ssl --single-transaction --quick --skip-comments %s %s | gzip > %s',
            escapeshellarg($credentialsFile),
            $ignoreOptions,
            escapeshellarg($database),
            escapeshellarg($backupFilepath),
        );

        return $command;
    }

    private function setupBackupDirectory(): void
    {
        if (is_dir(storage_path('app/backups/'))) return;
        $created = mkdir(storage_path('app/backups/'), 0755, true);

        if (!$created) {
            throw new \Exception('Failed to create temp backup directory');
        }
    }

    private function createCredentialsFile(): void
    {
        $config = [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'user' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];

        $iniContent = "[client]\n";
        foreach ($config as $key => $value) {
            // Escape double quotes.
            $safeValue = str_replace('"', '\"', $value);
            $iniContent .= "{$key} = \"{$safeValue}\"\n";
        }

        file_put_contents($this->credentialsFile, $iniContent);

        // NOTE: It should be restricted to owner-only access because it contains credentials.
        // the leading '0' is required for octal permissions.
        chmod($this->credentialsFile, 0600);
    }
}
