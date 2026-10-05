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
        Schema::table('monitoring_results', function (Blueprint $table) {
            // 1. Tambahkan kolom question_id jika belum ada
            if (!Schema::hasColumn('monitoring_results', 'question_id')) {
                $table->foreignId('question_id')->after('training_id')->constrained('evaluation_questions')->onDelete('cascade');
            }

            // 2. Hapus kolom 'question' (string) yang lama jika ada agar tidak redundan
            if (Schema::hasColumn('monitoring_results', 'question')) {
                $table->dropColumn('question');
            }
            
            // 3. Pastikan kolom category juga ada jika Anda ingin membagi per kategori
            if (!Schema::hasColumn('monitoring_results', 'category')) {
                $table->string('category')->nullable()->after('question_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_results', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn(['question_id', 'category']);
            $table->string('question');
        });
    }
};
