<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 30)->unique();
            $table->string('nama');
            $table->string('nip_nik', 30)->nullable();
            $table->string('email');
            $table->string('kategori_kendala', 100);
            $table->text('deskripsi');
            $table->string('status', 20)->default('open');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};