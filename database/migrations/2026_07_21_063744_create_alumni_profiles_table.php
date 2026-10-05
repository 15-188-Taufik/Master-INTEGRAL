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
        if (!Schema::hasTable('alumni_profiles')) {
            Schema::create('alumni_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('participant_id')->constrained()->onDelete('cascade');
                $table->foreignId('training_id')->constrained()->onDelete('cascade');
                
                // Pendidikan
                $table->string('edu_during_training')->nullable();
                $table->string('edu_current')->nullable();
                
                // Pangkat/Golongan
                $table->string('rank_during_training')->nullable();
                $table->string('rank_current')->nullable();
                
                // Jabatan
                $table->string('pos_during_training')->nullable();
                $table->string('pos_current')->nullable();
                
                // Unit Kerja / Perangkat Daerah
                $table->string('unit_during_training')->nullable();
                $table->string('unit_current')->nullable();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
