<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->string('name');
            $table->enum('project_type', ['tkdn_jasa', 'tkdn_barang_jasa'])->default('tkdn_jasa');
            $table->enum('status', ['draft', 'on_progress', 'completed'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('description')->nullable();
            $table->enum('category', ['Internal', 'External'])->nullable(); // New category field
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Drop foreign key constraints first if table exists
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
            });
        }
        
        Schema::dropIfExists('projects');
    }
};
