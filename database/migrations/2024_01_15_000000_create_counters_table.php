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
        if (!Schema::hasTable('counters')) {
            Schema::create('counters', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // worker, material, equipment
            $table->string('prefix')->nullable(); // WK, MT, EQ
            $table->unsignedInteger('current_number')->default(0);
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->timestamps();

            // Composite unique index untuk memastikan tidak ada duplikasi
            $table->unique(['entity_type', 'year', 'month']);
            
            // Index untuk performa query
            $table->index(['entity_type', 'year', 'month']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
