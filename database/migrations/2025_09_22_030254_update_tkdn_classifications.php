<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Material table
        DB::table('material')->where('classification_tkdn', '3.1 - Manajemen Proyek dan Perekayasaan')
            ->update(['classification_tkdn' => '3.1 - Overhead & Manajemen']);
        
        DB::table('material')->where('classification_tkdn', '3.2 - Alat Kerja')
            ->update(['classification_tkdn' => '3.2 - Alat / Fasilitas Kerja']);
        
        DB::table('material')->where('classification_tkdn', '3.3 - Konstruksi dan fabrikasi')
            ->update(['classification_tkdn' => '3.3 - Konstruksi Fabrikasi']);
        
        DB::table('material')->where('classification_tkdn', '3.4 - Jasa Umum')
            ->update(['classification_tkdn' => '3.4 - Peralatan (Jasa Umum)']);
        
        DB::table('material')->where('classification_tkdn', '3.5 - Rekapitulasi')
            ->update(['classification_tkdn' => '3.5 - Summary']);
        
        DB::table('material')->where('classification_tkdn', '4.1 - Material Langsung (Bahan Baku)')
            ->update(['classification_tkdn' => '4.1 - Material (Bahan Baku)']);
        
        DB::table('material')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
            ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
        DB::table('material')->where('classification_tkdn', '4.3 - Manajemen Proyek & Perekayasaan')
            ->update(['classification_tkdn' => '4.3 - Overhead & Manajemen']);
        
        DB::table('material')->where('classification_tkdn', '4.4 - Alat Kerja')
            ->update(['classification_tkdn' => '4.4 - Alat / Fasilitas Kerja']);
        
        DB::table('material')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
            ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
        DB::table('material')->where('classification_tkdn', '4.6 - Jasa Umum')
            ->update(['classification_tkdn' => '4.6 - Peralatan (Jasa Umum)']);
        
        DB::table('material')->where('classification_tkdn', '4.7 - Rekapitulasi')
            ->update(['classification_tkdn' => '4.7 - Summary']);

        // Update Workers table
        DB::table('workers')->where('classification_tkdn', '3.1 - Manajemen Proyek dan Perekayasaan')
            ->update(['classification_tkdn' => '3.1 - Overhead & Manajemen']);
        
        DB::table('workers')->where('classification_tkdn', '3.2 - Alat Kerja')
            ->update(['classification_tkdn' => '3.2 - Alat / Fasilitas Kerja']);
        
        DB::table('workers')->where('classification_tkdn', '3.3 - Konstruksi dan fabrikasi')
            ->update(['classification_tkdn' => '3.3 - Konstruksi Fabrikasi']);
        
        DB::table('workers')->where('classification_tkdn', '3.4 - Jasa Umum')
            ->update(['classification_tkdn' => '3.4 - Peralatan (Jasa Umum)']);
        
        DB::table('workers')->where('classification_tkdn', '3.5 - Rekapitulasi')
            ->update(['classification_tkdn' => '3.5 - Summary']);
        
        DB::table('workers')->where('classification_tkdn', '4.1 - Material Langsung (Bahan Baku)')
            ->update(['classification_tkdn' => '4.1 - Material (Bahan Baku)']);
        
        DB::table('workers')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
            ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
        DB::table('workers')->where('classification_tkdn', '4.3 - Manajemen Proyek & Perekayasaan')
            ->update(['classification_tkdn' => '4.3 - Overhead & Manajemen']);
        
        DB::table('workers')->where('classification_tkdn', '4.4 - Alat Kerja')
            ->update(['classification_tkdn' => '4.4 - Alat / Fasilitas Kerja']);
        
        DB::table('workers')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
            ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
        DB::table('workers')->where('classification_tkdn', '4.6 - Jasa Umum')
            ->update(['classification_tkdn' => '4.6 - Peralatan (Jasa Umum)']);
        
        DB::table('workers')->where('classification_tkdn', '4.7 - Rekapitulasi')
            ->update(['classification_tkdn' => '4.7 - Summary']);

        // Update Equipment table
        DB::table('equipment')->where('classification_tkdn', '3.1 - Manajemen Proyek dan Perekayasaan')
            ->update(['classification_tkdn' => '3.1 - Overhead & Manajemen']);
        
        DB::table('equipment')->where('classification_tkdn', '3.2 - Alat Kerja')
            ->update(['classification_tkdn' => '3.2 - Alat / Fasilitas Kerja']);
        
        DB::table('equipment')->where('classification_tkdn', '3.3 - Konstruksi dan fabrikasi')
            ->update(['classification_tkdn' => '3.3 - Konstruksi Fabrikasi']);
        
        DB::table('equipment')->where('classification_tkdn', '3.4 - Jasa Umum')
            ->update(['classification_tkdn' => '3.4 - Peralatan (Jasa Umum)']);
        
        DB::table('equipment')->where('classification_tkdn', '3.5 - Rekapitulasi')
            ->update(['classification_tkdn' => '3.5 - Summary']);
        
        DB::table('equipment')->where('classification_tkdn', '4.1 - Material Langsung (Bahan Baku)')
            ->update(['classification_tkdn' => '4.1 - Material (Bahan Baku)']);
        
        DB::table('equipment')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
            ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
        DB::table('equipment')->where('classification_tkdn', '4.3 - Manajemen Proyek & Perekayasaan')
            ->update(['classification_tkdn' => '4.3 - Overhead & Manajemen']);
        
        DB::table('equipment')->where('classification_tkdn', '4.4 - Alat Kerja')
            ->update(['classification_tkdn' => '4.4 - Alat / Fasilitas Kerja']);
        
        DB::table('equipment')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
            ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
        DB::table('equipment')->where('classification_tkdn', '4.6 - Jasa Umum')
            ->update(['classification_tkdn' => '4.6 - Peralatan (Jasa Umum)']);
        
        DB::table('equipment')->where('classification_tkdn', '4.7 - Rekapitulasi')
            ->update(['classification_tkdn' => '4.7 - Summary']);
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     // Reverse Material table
    //     DB::table('material')->where('classification_tkdn', '3.1 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '3.1 - Manajemen Proyek dan Perekayasaan']);
        
    //     DB::table('material')->where('classification_tkdn', '3.2 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '3.2 - Alat Kerja']);
        
    //     DB::table('material')->where('classification_tkdn', '3.3 - Konstruksi Fabrikasi')
    //         ->update(['classification_tkdn' => '3.3 - Konstruksi dan fabrikasi']);
        
    //     DB::table('material')->where('classification_tkdn', '3.4 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '3.4 - Jasa Umum']);
        
    //     DB::table('material')->where('classification_tkdn', '3.5 - Summary')
    //         ->update(['classification_tkdn' => '3.5 - Rekapitulasi']);
        
    //     DB::table('material')->where('classification_tkdn', '4.1 - Material (Bahan Baku)')
    //         ->update(['classification_tkdn' => '4.1 - Material Langsung (Bahan Baku)']);
        
    //     DB::table('material')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
    //         ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
    //     DB::table('material')->where('classification_tkdn', '4.3 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '4.3 - Manajemen Proyek & Perekayasaan']);
        
    //     DB::table('material')->where('classification_tkdn', '4.4 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '4.4 - Alat Kerja']);
        
    //     DB::table('material')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
    //         ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
    //     DB::table('material')->where('classification_tkdn', '4.6 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '4.6 - Jasa Umum']);
        
    //     DB::table('material')->where('classification_tkdn', '4.7 - Summary')
    //         ->update(['classification_tkdn' => '4.7 - Rekapitulasi']);

    //     // Reverse Workers table
    //     DB::table('workers')->where('classification_tkdn', '3.1 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '3.1 - Manajemen Proyek dan Perekayasaan']);
        
    //     DB::table('workers')->where('classification_tkdn', '3.2 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '3.2 - Alat Kerja']);
        
    //     DB::table('workers')->where('classification_tkdn', '3.3 - Konstruksi Fabrikasi')
    //         ->update(['classification_tkdn' => '3.3 - Konstruksi dan fabrikasi']);
        
    //     DB::table('workers')->where('classification_tkdn', '3.4 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '3.4 - Jasa Umum']);
        
    //     DB::table('workers')->where('classification_tkdn', '3.5 - Summary')
    //         ->update(['classification_tkdn' => '3.5 - Rekapitulasi']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.1 - Material (Bahan Baku)')
    //         ->update(['classification_tkdn' => '4.1 - Material Langsung (Bahan Baku)']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
    //         ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.3 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '4.3 - Manajemen Proyek & Perekayasaan']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.4 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '4.4 - Alat Kerja']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
    //         ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.6 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '4.6 - Jasa Umum']);
        
    //     DB::table('workers')->where('classification_tkdn', '4.7 - Summary')
    //         ->update(['classification_tkdn' => '4.7 - Rekapitulasi']);

    //     // Reverse Equipment table
    //     DB::table('equipment')->where('classification_tkdn', '3.1 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '3.1 - Manajemen Proyek dan Perekayasaan']);
        
    //     DB::table('equipment')->where('classification_tkdn', '3.2 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '3.2 - Alat Kerja']);
        
    //     DB::table('equipment')->where('classification_tkdn', '3.3 - Konstruksi Fabrikasi')
    //         ->update(['classification_tkdn' => '3.3 - Konstruksi dan fabrikasi']);
        
    //     DB::table('equipment')->where('classification_tkdn', '3.4 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '3.4 - Jasa Umum']);
        
    //     DB::table('equipment')->where('classification_tkdn', '3.5 - Summary')
    //         ->update(['classification_tkdn' => '3.5 - Rekapitulasi']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.1 - Material (Bahan Baku)')
    //         ->update(['classification_tkdn' => '4.1 - Material Langsung (Bahan Baku)']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.2 - Peralatan (Barang Jadi)')
    //         ->update(['classification_tkdn' => '4.2 - Peralatan (Barang Jadi)']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.3 - Overhead & Manajemen')
    //         ->update(['classification_tkdn' => '4.3 - Manajemen Proyek & Perekayasaan']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.4 - Alat / Fasilitas Kerja')
    //         ->update(['classification_tkdn' => '4.4 - Alat Kerja']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.5 - Konstruksi & Fabrikasi')
    //         ->update(['classification_tkdn' => '4.5 - Konstruksi & Fabrikasi']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.6 - Peralatan (Jasa Umum)')
    //         ->update(['classification_tkdn' => '4.6 - Jasa Umum']);
        
    //     DB::table('equipment')->where('classification_tkdn', '4.7 - Summary')
    //         ->update(['classification_tkdn' => '4.7 - Rekapitulasi']);
    // }
};
