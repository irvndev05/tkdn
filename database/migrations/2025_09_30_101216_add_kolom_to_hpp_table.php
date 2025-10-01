<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hpps', function (Blueprint $table) {
            $table->text('name_hpp', 225)->nullable()->after('project_id');
            // $table->decimal('volume', 15, 2)->default(0)->after('nama_hpp');
            // $table->string('satuan')->default("Unit")->after('volume');
            // $table->integer('durasi')->default(0)->after('satuan');
            // $table->string('satuan_durasi')->default("Hari")->after('durasi');
            // $table->integer('sub_grand_total_hpp_item')->default(0)->after('sub_total_hpp'); // Grand Total setelah dikalikan volume x durasi
        });

        Schema::table('hpp_items', function (Blueprint $table) {
            $table->text('name_ahs', 225)->nullable()->after('estimation_item_id'); // format nama_hpp: HPP - Nama Project
            $table->decimal('koefisien')->default(0)->after('duration_unit');
            $table->integer('jumlah')->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpps', function (Blueprint $table) {
            $table->dropColumn(['name_hpp']);
        });

        Schema::table('hpp_items', function (Blueprint $table) {
            $table->dropColumn(['name_hpp', 'koefisien', 'jumlah']);
        });
    }
};
