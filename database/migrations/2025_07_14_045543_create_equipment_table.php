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
        Schema::create('equipment', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->ulid('category_id')->nullable();
            $table->decimal('tkdn', 5, 2)->nullable(); // TKDN as percentage, e.g. 87.50
            $table->integer('period')->comment('Satuan Hari');
            $table->unsignedBigInteger('price');
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
