<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessEmailQueueCommand extends Command
{
    protected $signature = 'mail:process-queue';
    protected $description = 'Process pending email jobs from the queue';

    public function handle()
    {
        $this->info('Starting mail queue processing...');
        
        // Get jobs from the queue (assuming database driver)
        $pendingJobs = DB::table('jobs')
            ->where('queue', 'default')
            ->orderBy('id')
            ->limit(50) // Process in batches
            ->get();
            
        $count = count($pendingJobs);
        $this->info("Found {$count} pending jobs");
        
        if ($count === 0) {
            return 0;
        }
        
        // Process each job manually
        foreach ($pendingJobs as $job) {
            try {
                $this->info("Processing job ID: {$job->id}");
                
                // Run the queue:work command for just this job
                $exitCode = $this->call('queue:work', [
                    '--once' => true,
                    '--queue' => $job->queue,
                ]);
                
                if ($exitCode !== 0) {
                    $this->error("Failed to process job ID: {$job->id}");
                    Log::error("Failed to process email job: {$job->id}");
                }
                
                // Small pause to not overload the server
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("Exception while processing job: " . $e->getMessage());
                Log::error("Mail queue exception: " . $e->getMessage());
            }
        }
        
        $this->info('Mail queue processing completed');
        return 0;
    }
}