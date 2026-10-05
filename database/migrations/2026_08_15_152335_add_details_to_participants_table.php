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
        Schema::table('participants', function (Blueprint $table) {
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->nullable()->after('name');
            $table->string('provinsi')->nullable()->after('instansi');
            $table->string('kabupaten_kota')->nullable()->after('provinsi');
            $table->string('status_kepegawaian')->nullable()->after('kabupaten_kota'); // ASN / NON-ASN / PNS
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['gender', 'provinsi', 'kabupaten_kota', 'status_kepegawaian']);
        });
    }

};
