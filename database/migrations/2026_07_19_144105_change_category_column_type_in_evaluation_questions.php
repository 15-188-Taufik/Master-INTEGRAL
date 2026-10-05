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
        Schema::table('evaluation_questions', function (Blueprint $table) {
            // Mengubah category menjadi string biasa agar muat teks panjang
            $table->string('category')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_questions', function (Blueprint $table) {
            $table->string('category', 50)->change();
        });
    }
};
