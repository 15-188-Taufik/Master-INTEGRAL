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
        Schema::table('evaluation_questions', function (Blueprint $table) {
            // Tambahkan kolom metode setelah kolom sub_category atau category
            if (!Schema::hasColumn('evaluation_questions', 'metode')) {
                $table->string('metode')->nullable()->default('semua')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_questions', function (Blueprint $table) {
            $table->dropColumn('metode');
        });
    }
};
