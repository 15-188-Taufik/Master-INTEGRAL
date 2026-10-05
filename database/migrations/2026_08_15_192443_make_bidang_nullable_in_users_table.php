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
        Schema::table('users', function (Blueprint $table) {
            // Mengubah kolom bidang agar boleh dikosongkan (nullable)
            $table->string('bidang')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika di-rollback, kembali ke keadaan awal (sesuaikan tipe datanya)
            $table->string('bidang')->nullable(false)->change();
        });
    }
};
