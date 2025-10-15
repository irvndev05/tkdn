<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Worker;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * PEKERJA
     */
    public function run(): void
    {
        // Get Pekerja category ID
        $workerCategory = Category::where('code', 'PJ')->first();
        $categoryId = $workerCategory ? $workerCategory->id : null;

        $workers = [
            // Overhead & Manajemen (classification_tkdn = 1)
            ['code' => 'PJ001', 'name' => 'Mandor', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 1, 'price' => 30000, 'tkdn' => 0], // 30,000 per jam
            ['code' => 'PJ012', 'name' => 'Arsiparis', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 1, 'price' => 35000, 'tkdn' => 0], // 35,000 per jam
            ['code' => 'PJ021', 'name' => 'Administrasi Keproyekan', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 1, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ026', 'name' => 'Pelaksana', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 1, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam

            // Alat Kerja / Fasilitas (classification_tkdn = 2)
            ['code' => 'PJ002', 'name' => 'Pekerja/ Kenek', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 2, 'price' => 15000, 'tkdn' => 0], // 15,000 per jam
            ['code' => 'PJ018', 'name' => 'Operator Alat Berat', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 2, 'price' => 30000, 'tkdn' => 0], // 30,000 per jam
            ['code' => 'PJ019', 'name' => 'Pembantu Operator Alat berat', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 2, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam

            // Konstruksi & Fabrikasi (classification_tkdn = 3)
            ['code' => 'PJ003', 'name' => 'Tukang Gali', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 18000, 'tkdn' => 0], // 18,000 per jam
            ['code' => 'PJ004', 'name' => 'Kepala Tukang Batu', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ005', 'name' => 'Tukang Batu', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ006', 'name' => 'Kepala Tukang Kayu', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ007', 'name' => 'Tukang Kayu', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ008', 'name' => 'Kepala Tukang Besi', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ009', 'name' => 'Tukang Besi', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ010', 'name' => 'Kepala Tukang Cat', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ011', 'name' => 'Tukang Cat', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ013', 'name' => 'Tukang Aspal', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 15000, 'tkdn' => 0], // 15,000 per jam
            ['code' => 'PJ014', 'name' => 'Instalator', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 25000, 'tkdn' => 0], // 25,000 per jam
            ['code' => 'PJ015', 'name' => 'Pembantu Instalator', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ016', 'name' => 'Kepala Tuaking Pasang Pipa/Ledeng', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ017', 'name' => 'Tukang Pasang Pipa', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 15000, 'tkdn' => 0], // 15,000 per jam
            ['code' => 'PJ020', 'name' => 'Tukang Las', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 3, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam

            // Peralatan (Jasa Umum) (classification_tkdn = 4)
            ['code' => 'PJ022', 'name' => 'Tukang Babat Rumput', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 4, 'price' => 15000, 'tkdn' => 0], // 15,000 per jam
            ['code' => 'PJ023', 'name' => 'Angkut Bongkar Muat', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 4, 'price' => 18000, 'tkdn' => 0], // 18,000 per jam
            ['code' => 'PJ024', 'name' => 'Driver', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 4, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ025', 'name' => 'Security', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 4, 'price' => 20000, 'tkdn' => 0], // 20,000 per jam
            ['code' => 'PJ027', 'name' => 'General Worker', 'unit' => 'OH', 'category_id' => $categoryId, 'classification_tkdn' => 4, 'price' => 18000, 'tkdn' => 0], // 18,000 per jam
        ];

        $created = 0;
        $updated = 0;

        foreach ($workers as $worker) {
            $existing = Worker::where('code', $worker['code'])->first();
            if ($existing) {
                $existing->update($worker);
                $updated++;
            } else {
                Worker::create($worker);
                $created++;
            }
        }

        $this->command->info('WorkerSeeder completed!');
        $this->command->info("Created: {$created} workers");
        $this->command->info("Updated: {$updated} workers");
        $this->command->info('Total: ' . ($created + $updated) . ' workers processed');

        // Show classification distribution
        $classifications = Worker::select('classification_tkdn')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('classification_tkdn')
            ->get();

        $this->command->info('Classification distribution:');
        foreach ($classifications as $classification) {
            $this->command->info("  {$classification->classification_tkdn}: {$classification->count} workers");
        }
    }
}
