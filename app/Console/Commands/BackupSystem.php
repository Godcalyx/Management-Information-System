<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupSystem extends Command
{
    protected $signature = 'backup:system';
    protected $description = 'Full system backup including files and database, folder-based, with cleanup';

    // How many days to keep backups
    protected $keepDays = 7;

    // Folders to exclude from backup
    protected $exclude = [
        'vendor',
        'node_modules',
        'public/storage',
        'storage/backups',
    ];

    public function handle()
    {
        $backupBase = storage_path('backups');

        // --- Ensure backup folder exists ---
        if (!file_exists($backupBase)) mkdir($backupBase, 0755, true);

        $timestamp = now()->format('Ymd_His');
        $backupDir = $backupBase . DIRECTORY_SEPARATOR . $timestamp;

        if (!file_exists($backupDir)) mkdir($backupDir, 0755, true);

        // --- Backup project files ---
        $this->info("Backing up project files...");
        $this->copyDirectory(base_path(), $backupDir);

        // --- Backup database ---
        $dbFile = $backupDir . DIRECTORY_SEPARATOR . "db_backup.sql";
        $dbBackupSucceeded = $this->backupDatabase($dbFile);

        if ($dbBackupSucceeded) {
            $this->info("Database backup successful: {$dbFile}");
        } else {
            $this->warn("Database backup skipped or failed.");
        }

        $this->info("Backup completed successfully at: {$backupDir}");

        // --- Cleanup old backups ---
        $this->cleanupOldBackups($backupBase);
    }

    protected function copyDirectory($source, $destination)
{
    $items = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST
    );

    $baseLength = strlen($source) + 1;

    foreach ($items as $item) {
        $relativePath = substr($item->getPathname(), $baseLength);

        // Skip excluded folders
        foreach ($this->exclude as $skip) {
            if (str_starts_with($relativePath, $skip)) continue 2;
        }

        $destPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

        if ($item->isDir()) {
            if (!file_exists($destPath)) mkdir($destPath, 0755, true);
        } else {
            // Only copy if file actually exists
            if (file_exists($item->getPathname())) {
                copy($item->getPathname(), $destPath);
            } else {
                $this->warn("Skipped missing file: {$item->getPathname()}");
            }
        }
    }
}


    protected function backupDatabase($filePath)
    {
        $connection = config('database.default');
        $dbConfig = config("database.connections.{$connection}");

        $host = $dbConfig['host'];
        $port = $dbConfig['port'] ?? 3306;
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        $mysqldump = 'C:\xammpp\mysql\bin\mysqldump.exe';
        if (!file_exists($mysqldump)) {
            $this->warn("mysqldump not found at {$mysqldump}, skipping database backup.");
            return false;
        }

        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s --port=%d %s > "%s"',
            $mysqldump,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            $port,
            escapeshellarg($database),
            $filePath
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($filePath)) {
            $this->warn("Database backup failed. Check mysqldump credentials or path.");
            return false;
        }

        return true;
    }

    protected function cleanupOldBackups($backupBase)
    {
        $folders = glob($backupBase . '/*', GLOB_ONLYDIR);
        $now = time();
        $deleted = 0;

        foreach ($folders as $folder) {
            if (is_dir($folder) && ($now - filemtime($folder)) > $this->keepDays * 86400) {
                $this->deleteDirectory($folder);
                $deleted++;
            }
        }

        if ($deleted > 0) $this->info("Deleted {$deleted} old backup(s) older than {$this->keepDays} days.");
    }

    protected function deleteDirectory($dir)
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($dir);
    }
}
