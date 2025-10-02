<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('service_items')) {
            Schema::create('service_items', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->ulid('service_id');
            $table->ulid('estimation_item_id')->nullable();
            $table->integer('item_number'); // No. urut
            $table->string('tkdn_classification')->nullable();
            $table->string('description'); // Uraian
            $table->string('qualification')->nullable(); // Kualifikasi
            $table->enum('nationality', ['WNI', 'WNA'])->default('WNI'); // Kewarganegaraan
            $table->decimal('tkdn_percentage', 5, 2)->default(0); // TKDN (%)
            $table->integer('quantity')->default(1); // Jumlah
            $table->decimal('duration', 10, 2)->default(0); // Durasi
            $table->string('duration_unit')->default('Is'); // Satuan durasi (Is = Isi)
            $table->decimal('wage', 15, 2)->default(0); // Upah (Rupiah)
            $table->decimal('domestic_cost', 15, 2)->default(0); // Biaya KDN
            $table->decimal('foreign_cost', 15, 2)->default(0); // Biaya KLN
            $table->decimal('total_cost', 15, 2)->default(0); // Total biaya
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('service_items');
    }
};
