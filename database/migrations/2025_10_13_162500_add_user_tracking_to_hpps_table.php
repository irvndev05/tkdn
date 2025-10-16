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
        Schema::table('hpps', function (Blueprint $table) {
            $table->string('created_by')->nullable()->after('status');
            $table->string('updated_by')->nullable()->after('created_by');
            $table->string('approved_by')->nullable()->after('updated_by');
            $table->string('rejected_by')->nullable()->after('approved_by');
            $table->string('submitted_by')->nullable()->after('rejected_by');
            $table->timestamp('approved_at')->nullable()->after('submitted_by');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->timestamp('submitted_at')->nullable()->after('rejected_at');
            $table->text('approval_notes')->nullable()->after('submitted_at');
            $table->text('rejection_notes')->nullable()->after('approval_notes');
            
            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpps', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['submitted_by']);
            
            $table->dropColumn([
                'created_by',
                'updated_by',
                'approved_by',
                'rejected_by',
                'submitted_by',
                'approved_at',
                'rejected_at',
                'submitted_at',
                'approval_notes',
                'rejection_notes'
            ]);
        });
    }
};