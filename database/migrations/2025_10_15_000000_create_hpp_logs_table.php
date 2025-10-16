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
        Schema::create('hpp_logs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('hpp_id');
            $table->string('user_id');
            $table->string('action'); // created, updated, submitted, approved, rejected, commented
            $table->string('status_from')->nullable(); // previous status
            $table->string('status_to')->nullable(); // new status
            $table->text('notes')->nullable(); // comments or notes
            $table->json('changes')->nullable(); // detailed changes made
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('hpp_id')->references('id')->on('hpps')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['hpp_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_logs');
    }
};