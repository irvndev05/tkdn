<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->ulid('id')->primary();
            $table->ulid('project_id');
            $table->string('service_name'); // Nama Jasa
            $table->string('provider_name')->nullable(); // Penyedia Barang/Jasa
            $table->text('provider_address')->nullable(); // Alamat
            $table->string('user_name')->nullable(); // Pengguna Barang/Jasa
            $table->string('document_number')->nullable(); // No. Dokumen Jasa
            $table->decimal('total_domestic_cost', 15, 2)->default(0); // Total biaya KDN
            $table->decimal('total_foreign_cost', 15, 2)->default(0); // Total biaya KLN
            $table->decimal('total_cost', 15, 2)->default(0); // Total biaya
            $table->decimal('tkdn_percentage', 5, 2)->default(0); // Persentase TKDN
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'generated'])->default('draft');
            $table->enum('service_type', ['project', 'equipment', 'construction'])->default('project');
            $table->string('tkdn_classification')->nullable();
            $table->enum('form_category', ['tkdn_jasa', 'tkdn_barang_jasa'])->default('tkdn_jasa');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // Drop foreign key constraints first if table exists
        if (Schema::hasTable('service_items')) {
            Schema::table('service_items', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
            });
        }
        
        Schema::dropIfExists('services');
    }
};
