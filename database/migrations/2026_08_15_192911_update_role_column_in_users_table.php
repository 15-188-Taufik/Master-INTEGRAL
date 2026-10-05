<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pilihan A: Ubah ke string agar bebas (Disarankan)
            $table->string('role')->change(); 
            
            // Pilihan B: Tetap Enum tapi ditambah participant
            // $table->enum('role', ['superadmin', 'admin_bidang', 'participant'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke asal jika rollback
            $table->enum('role', ['superadmin', 'admin_bidang'])->change();
        });
    }
};
