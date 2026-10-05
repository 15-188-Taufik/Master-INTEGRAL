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
            // Tambahkan training_id agar soal bisa khusus untuk 1 pelatihan
            // Jika NULL, berarti soal global untuk jenis pelatihan tersebut
            $table->foreignId('training_id')->nullable()->after('training_type')->constrained('trainings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
