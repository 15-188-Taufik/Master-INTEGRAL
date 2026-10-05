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
            // Mengubah tipe kolom menjadi string agar bisa menampung 'ya_tidak'
            $table->string('type')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_questions', function (Blueprint $table) {
            // Kembalikan ke enum jika diperlukan, tapi tambahkan ya_tidak
            $table->enum('type', ['slider', 'text', 'dropdown', 'ya_tidak'])->change();
        });
    }
};
