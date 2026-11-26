<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan')->nullable();
            }
            if (!Schema::hasColumn('users', 'kelurahan')) {
                $table->string('kelurahan')->nullable();
            }
        });

        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'kecamatan')) {
                $table->string('kecamatan')->nullable();
            }
            if (!Schema::hasColumn('participants', 'kelurahan')) {
                $table->string('kelurahan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });
    }
};