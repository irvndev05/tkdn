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
        Schema::table('services', function (Blueprint $table) {
            // Add approval tracking fields if they don't exist
            if (!Schema::hasColumn('services', 'approved_by')) {
                $table->string('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('services', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('services', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('services', 'notes')) {
                $table->text('notes')->nullable()->after('approval_notes');
            }
            if (!Schema::hasColumn('services', 'updated_by')) {
                $table->string('updated_by')->nullable()->after('notes');
            }
            
            // Add foreign keys
            if (!Schema::hasColumn('services', 'approved_by')) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('services', 'updated_by')) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('services', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('services', 'approval_notes')) {
                $table->dropColumn('approval_notes');
            }
            if (Schema::hasColumn('services', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('services', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });
    }
};
