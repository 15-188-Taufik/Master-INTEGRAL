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
        Schema::create('evaluation_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['penyelenggara', 'narasumber']);
            $table->string('name'); // Nama Form
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('set null'); // Jika narasumber
            $table->string('target_name')->nullable(); // Nama Pengajar atau Nama Instansi
            $table->string('materi')->nullable(); // Jika narasumber
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_form_l1_s');
    }
};
