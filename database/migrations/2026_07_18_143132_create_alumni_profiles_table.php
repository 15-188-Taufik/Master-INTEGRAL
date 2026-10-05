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
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained();
            $table->foreignId('training_id')->constrained();
            // Riwayat Pendidikan
            $table->string('edu_during_training');
            $table->string('edu_current');
            // Pangkat/Golongan
            $table->string('rank_during_training');
            $table->string('rank_current');
            // Jabatan & Unit Kerja
            $table->string('pos_during_training');
            $table->string('pos_current');
            $table->string('unit_during_training');
            $table->string('unit_current');
            $table->string('dept_during_training');
            $table->string('dept_current');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
