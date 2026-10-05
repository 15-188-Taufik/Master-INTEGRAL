<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('trainings', function (Blueprint $table) {
            $table->string('invitation_code', 10)->nullable()->after('nama_pelatihan');
        });
        Schema::table('participants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('training_id')->constrained('users')->onDelete('cascade');
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
