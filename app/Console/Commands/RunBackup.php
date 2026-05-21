<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunBackup extends Command
{
    protected $signature = 'system:backup';
    protected $description = 'Run automatic system backup';

    public function handle()
    {
        $this->info('Starting backup...');
        try {
            \Artisan::call('backup:run', [
                '--only-db' => false,  // remove to backup files too
            ]);
            $this->info('Backup completed successfully!');
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
        }
    }
}
