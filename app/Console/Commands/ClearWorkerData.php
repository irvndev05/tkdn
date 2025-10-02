<?php

namespace App\Console\Commands;

use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearWorkerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:worker {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from workers table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $totalRecords = Worker::count();
        
        if ($totalRecords === 0) {
            $this->info('No worker records found to delete.');
            return 0;
        }

        $this->warn("Found {$totalRecords} worker records to delete.");
        
        // Ask for confirmation unless --force flag is used
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL worker data? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting to clear worker data...');
        
        try {
            DB::beginTransaction();
            
            // Delete all worker records
            Worker::query()->delete();
            
            // Reset auto increment counter if using MySQL
            DB::statement('ALTER TABLE workers AUTO_INCREMENT = 1');
            
            DB::commit();
            
            $this->info("✅ Successfully deleted {$totalRecords} worker records.");
            $this->info('Workers table has been cleared and counter reset.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error occurred while clearing worker data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
