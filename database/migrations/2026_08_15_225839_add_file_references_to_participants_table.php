<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('participants', function (Blueprint $table) {
            // Menyimpan ID dari tabel files
            $table->foreignId('biodata_file_id')->nullable()->constrained('files')->onDelete('set null');
            $table->foreignId('surat_tugas_file_id')->nullable()->constrained('files')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            //
        });
    }
};
