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
        });

        Schema::table('hpp_items', function (Blueprint $table) {
            $table->text('name_ahs', 225)->nullable()->after('estimation_item_id'); // format nama_hpp: HPP - Nama Project
            $table->integer('hpp_ahs_id')->nullable()->references('id')->on('hpp_ahs')->after('hpp_id');
            $table->decimal('koefisien')->default(0)->after('duration_unit');
            $table->integer('jumlah')->default(0)->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('hpps', function (Blueprint $table) {
        //     $table->dropColumn(['name_hpp']);
        // });

        // Schema::table('hpp_items', function (Blueprint $table) {
        //     $table->dropColumn(['name_hpp', 'koefisien', 'jumlah']);
        // });
    }
};
