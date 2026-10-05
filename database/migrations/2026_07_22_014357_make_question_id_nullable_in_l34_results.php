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
        Schema::table('evaluation_results_l34', function (Blueprint $table) {
            // Mengubah question_id menjadi nullable agar bisa menyimpan 'task' tanpa ID soal
            $table->foreignId('question_id')->nullable()->change();
            
            // Pastikan ada kolom training_id untuk filter progres
            if (!Schema::hasColumn('evaluation_results_l34', 'training_id')) {
                $table->foreignId('training_id')->nullable()->after('participant_id')->constrained('trainings')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluation_results_l34', function (Blueprint $table) {
            //
        });
    }
};
