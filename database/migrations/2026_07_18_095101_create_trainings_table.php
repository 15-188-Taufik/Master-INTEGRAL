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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('bidang');
            $table->string('nama_pelatihan');
            $table->enum('model', ['standar', 'blended']);
            $table->string('metode'); // klasikal / daring / blended
            $table->string('lokasi');
            $table->string('kerjasama')->nullable();
            $table->string('anggaran')->nullable();
            $table->string('angkatan');
            $table->integer('jumlah_peserta');
            $table->integer('jp'); // Jam Pelajaran
            
            // Tanggal Global
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');

            // Khusus Blended (Opsional)
            $table->date('tgl_mulai_klasikal')->nullable();
            $table->date('tgl_selesai_klasikal')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
