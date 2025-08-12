<?php

namespace App\Console\Commands;

use App\Services\ElderlyProfileRealtimeService;
use Illuminate\Console\Command;

class StartElderlyProfileRealtimeListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supabase:listen-elderly-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start real-time listener for elderly profiles from Supabase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting real-time listener for elderly profiles...');

        $realtimeService = new ElderlyProfileRealtimeService();
        
        try {
            $success = $realtimeService->startListening();
            
            if ($success) {
                $this->info('✅ Real-time listener started successfully');
                $this->info('Listening for changes in elderly_profiles table...');
                $this->info('Press Ctrl+C to stop');
                
                // Keep the command running
                while (true) {
                    sleep(10);
                    $this->info('Listener is still active...');
                }
            } else {
                $this->error('❌ Failed to start real-time listener');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error starting real-time listener: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
