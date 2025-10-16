<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        if (!Schema::hasTable('estimations')) {
            Schema::create('estimations', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->string('code')->nullable(); // Contoh: A.2.2
            $table->string('title'); // Judul Pekerjaan
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('total_unit_price')->default(0);
            $table->timestamps();
            });
        }
    }
    public function down() {
        Schema::dropIfExists('estimations');
    }
}; 