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
            $table->text('note')->nullable()->after('score');
            // Ubah score jadi nullable karena tidak semua soal pakai angka (paragraf/dropdown)
            $table->integer('score')->nullable()->change();
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
