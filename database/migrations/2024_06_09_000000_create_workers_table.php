<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit');
            $table->ulid('category_id')->nullable();
            $table->unsignedBigInteger('price');
            $table->decimal('tkdn', 5, 2)->nullable(); // tipe data di ubah ke decimal
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('workers');
    }
};
