<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define mapping for all possible old formats to new formats
        $classificationMappings = [
            // Format: "3.1 - Description" or "3.1" -> "Overhead & Manajemen"
            '3.1' => 'Overhead & Manajemen',
            '3.1 - Overhead & Manajemen' => 'Overhead & Manajemen',
            '3.1 - Manajemen Proyek dan Perekayasaan' => 'Overhead & Manajemen',
            '3.1 - Manajemen Proyek & Perekayasaan' => 'Overhead & Manajemen',

            // Format: "3.2 - Description" or "3.2" -> "Alat Kerja / Fasilitas"
            '3.2' => 'Alat Kerja / Fasilitas',
            '3.2 - Alat / Fasilitas Kerja' => 'Alat Kerja / Fasilitas',
            '3.2 - Alat Kerja' => 'Alat Kerja / Fasilitas',
            '3.2 - Alat Kerja / Fasilitas' => 'Alat Kerja / Fasilitas',

            // Format: "3.3 - Description" or "3.3" -> "Konstruksi & Fabrikasi"
            '3.3' => 'Konstruksi & Fabrikasi',
            '3.3 - Konstruksi Fabrikasi' => 'Konstruksi & Fabrikasi',
            '3.3 - Konstruksi dan Fabrikasi' => 'Konstruksi & Fabrikasi',
            '3.3 - Konstruksi & Fabrikasi' => 'Konstruksi & Fabrikasi',

            // Format: "3.4 - Description" or "3.4" -> "Peralatan (Jasa Umum)"
            '3.4' => 'Peralatan (Jasa Umum)',
            '3.4 - Peralatan (Jasa Umum)' => 'Peralatan (Jasa Umum)',
            '3.4 - Jasa Umum' => 'Peralatan (Jasa Umum)',

            // Format: "3.5 - Description" or "3.5" -> "Summary"
            '3.5' => 'Summary',
            '3.5 - Summary' => 'Summary',
            '3.5 - Rekapitulasi' => 'Summary',

            // Format: "4.1 - Description" or "4.1" -> "Material (Bahan Baku)"
            '4.1' => 'Material (Bahan Baku)',
            '4.1 - Material (Bahan Baku)' => 'Material (Bahan Baku)',
            '4.1 - Material Langsung (Bahan Baku)' => 'Material (Bahan Baku)',

            // Format: "4.2 - Description" or "4.2" -> "Peralatan (Barang Jadi)"
            '4.2' => 'Peralatan (Barang Jadi)',
            '4.2 - Peralatan (Barang Jadi)' => 'Peralatan (Barang Jadi)',

            // Format: "4.3 - Description" or "4.3" -> "Overhead & Manajemen"
            '4.3' => 'Overhead & Manajemen',
            '4.3 - Overhead & Manajemen' => 'Overhead & Manajemen',
            '4.3 - Manajemen Proyek & Perekayasaan' => 'Overhead & Manajemen',

            // Format: "4.4 - Description" or "4.4" -> "Alat Kerja / Fasilitas"
            '4.4' => 'Alat Kerja / Fasilitas',
            '4.4 - Alat / Fasilitas Kerja' => 'Alat Kerja / Fasilitas',
            '4.4 - Alat Kerja' => 'Alat Kerja / Fasilitas',

            // Format: "4.5 - Description" or "4.5" -> "Konstruksi & Fabrikasi"
            '4.5' => 'Konstruksi & Fabrikasi',
            '4.5 - Konstruksi & Fabrikasi' => 'Konstruksi & Fabrikasi',

            // Format: "4.6 - Description" or "4.6" -> "Peralatan (Jasa Umum)"
            '4.6' => 'Peralatan (Jasa Umum)',
            '4.6 - Peralatan (Jasa Umum)' => 'Peralatan (Jasa Umum)',
            '4.6 - Jasa Umum' => 'Peralatan (Jasa Umum)',

            // Format: "4.7 - Description" or "4.7" -> "Summary"
            '4.7' => 'Summary',
            '4.7 - Summary' => 'Summary',
            '4.7 - Rekapitulasi' => 'Summary',
        ];

        // Update all tables with comprehensive mapping
        $tables = ['material', 'workers', 'equipment'];

        foreach ($tables as $table) {
            foreach ($classificationMappings as $oldFormat => $newFormat) {
                DB::table($table)
                    ->where('classification_tkdn', $oldFormat)
                    ->update(['classification_tkdn' => $newFormat]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     // Define reverse mapping from new formats back to old formats
    //     $reverseMappings = [
    //         'Overhead & Manajemen' => '3.1 - Overhead & Manajemen',
    //         'Alat Kerja / Fasilitas' => '3.2 - Alat / Fasilitas Kerja',
    //         'Konstruksi & Fabrikasi' => '3.3 - Konstruksi Fabrikasi',
    //         'Peralatan (Jasa Umum)' => '3.4 - Peralatan (Jasa Umum)',
    //         'Summary' => '3.5 - Summary',
    //         'Material (Bahan Baku)' => '4.1 - Material (Bahan Baku)',
    //         'Peralatan (Barang Jadi)' => '4.2 - Peralatan (Barang Jadi)',
    //     ];

    //     // Reverse all tables
    //     $tables = ['material', 'workers', 'equipment'];

    //     foreach ($tables as $table) {
    //         foreach ($reverseMappings as $newFormat => $oldFormat) {
    //             DB::table($table)
    //                 ->where('classification_tkdn', $newFormat)
    //                 ->update(['classification_tkdn' => $oldFormat]);
    //         }
    //     }
    // }
};
