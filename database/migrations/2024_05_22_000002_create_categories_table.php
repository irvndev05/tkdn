<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('tkdn_type', ['tkdn_jasa', 'tkdn_barang_jasa'])->default('tkdn_barang_jasa');
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
