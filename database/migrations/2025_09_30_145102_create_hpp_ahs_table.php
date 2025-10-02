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
        Schema::create('hpp_ahs', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->ulid('hpp_id');
            $table->text('name_ahs', 225)->nullable(); // deskripsi nama AHS format: Code  - Title
            $table->decimal('volume', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->integer('duration')->nullable();
            $table->string('duration_unit')->nullable();
            $table->decimal('unit_price', 15, 2); // Total dari HPP Item
            $table->decimal('total_price', 15, 2); // Total Price = unit_price * volume * duration
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_ahs');
    }
};
