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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            // Kategori: monitoring, l1_narasumber, l1_penyelenggara, l3_mandiri, l3_atasan_rekan, l4
            $table->string('category'); 
            
            // Teks pertanyaan
            $table->text('question_text');
            
            // Metode: klasikal, blended, e-learning (untuk memfilter monitoring)
            $table->string('metode')->nullable()->default('semua');
            
            // Tipe jawaban: slider (10-100), text (saran), ya_tidak (monitoring)
            $table->string('type')->default('slider');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
