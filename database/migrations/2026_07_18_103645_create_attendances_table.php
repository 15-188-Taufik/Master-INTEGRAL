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
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')->constrained()->onDelete('cascade');
                $table->foreignId('participant_id')->constrained()->onDelete('cascade');
                $table->enum('status', ['hadir', 'izin', 'sakit']);
                $table->timestamp('check_in_at')->nullable();
                $table->string('keterangan')->nullable(); // Alasan jika izin/sakit
                $table->timestamps();
                
                // Mencegah satu peserta absen 2x di sesi yang sama
                $table->unique(['schedule_id', 'participant_id']);
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
