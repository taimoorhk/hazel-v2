<?php

namespace App\Console\Commands;

use App\Services\ElderlyProfileRealtimeService;
use Illuminate\Console\Command;

class SyncElderlyProfilesFromSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:sync-elderly-profiles {--force : Force sync even if profiles exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync elderly profiles from Supabase to Laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting elderly profiles sync from Supabase...');

        $realtimeService = new ElderlyProfileRealtimeService();
        
        try {
            $success = $realtimeService->syncAllProfiles();
            
            if ($success) {
                $this->info('✅ Successfully synced elderly profiles from Supabase');
            } else {
                $this->error('❌ Failed to sync elderly profiles from Supabase');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error during sync: ' . $e->getMessage());
            return 1;
        }

        $this->info('Sync completed successfully!');
        return 0;
    }
}
