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
        Schema::create('evaluation_results_l1', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained();
            $table->foreignId('participant_id')->constrained();
            $table->foreignId('schedule_id')->nullable()->constrained(); // Null jika evaluasi penyelenggara
            $table->foreignId('question_id')->constrained('evaluation_questions');
            $table->integer('score')->nullable(); // Nilai 10-100
            $table->text('note')->nullable(); // Jika tipe soal adalah text/saran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_result_l1_s');
    }
};
