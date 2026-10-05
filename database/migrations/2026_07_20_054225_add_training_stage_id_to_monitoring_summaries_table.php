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
        Schema::table('monitoring_summaries', function (Blueprint $table) {
            // Tambahkan kolom training_stage_id setelah training_id
            if (!Schema::hasColumn('monitoring_summaries', 'training_stage_id')) {
                $table->foreignId('training_stage_id')->nullable()->after('training_id')->constrained('training_stages')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_summaries', function (Blueprint $table) {
            $table->dropForeign(['training_stage_id']);
            $table->dropColumn('training_stage_id');
        });
    }
};
