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
        Schema::create('evaluation_results_l34', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained();
            $table->enum('evaluator_role', ['mandiri', 'rekan', 'atasan']);
            $table->string('evaluator_name'); // Nama penilai (jika rekan/atasan)
            $table->foreignId('question_id')->constrained('evaluation_questions');
            $table->integer('score'); // 10-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_result_l34_s');
    }
};
