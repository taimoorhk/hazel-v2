<?php

namespace App\Console\Commands;

use App\Jobs\SyncSupabaseUsersJob;
use Illuminate\Console\Command;

class DispatchSyncJob extends Command
{
    protected $signature = 'supabase:dispatch-sync';
    protected $description = 'Dispatch a job to sync Supabase users to Laravel database';

    public function handle()
    {
        $this->info('Dispatching Supabase users sync job...');
        
        try {
            SyncSupabaseUsersJob::dispatch();
            $this->info('Sync job dispatched successfully!');
            $this->info('Check the logs for sync progress.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to dispatch sync job: ' . $e->getMessage());
            return 1;
        }
    }
} 