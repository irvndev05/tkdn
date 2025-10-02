<?php

namespace App\Console\Commands;

use App\Models\Material;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearMaterialData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:material {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from material table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $totalRecords = Material::count();
        
        if ($totalRecords === 0) {
            $this->info('No material records found to delete.');
            return 0;
        }

        $this->warn("Found {$totalRecords} material records to delete.");
        
        // Ask for confirmation unless --force flag is used
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL material data? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting to clear material data...');
        
        try {
            DB::beginTransaction();
            
            // Delete all material records
            Material::query()->delete();
            
            // Reset auto increment counter if using MySQL
            DB::statement('ALTER TABLE material AUTO_INCREMENT = 1');
            
            DB::commit();
            
            $this->info("✅ Successfully deleted {$totalRecords} material records.");
            $this->info('Material table has been cleared and counter reset.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error occurred while clearing material data: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
