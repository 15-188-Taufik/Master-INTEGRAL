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
        Schema::table('questions', function (Blueprint $table) {
            // Jenis: PKTI/PKTU, CPNS, PKP, PKA, PKN
            if (!Schema::hasColumn('questions', 'training_type')) {
                $table->string('training_type')->nullable(); 
            }
            // Sub-Kategori: penyelenggara, narasumber, sarpras, tenaga_kediklatan
            if (!Schema::hasColumn('questions', 'sub_category')) {
                $table->string('sub_category')->nullable();
            }
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
