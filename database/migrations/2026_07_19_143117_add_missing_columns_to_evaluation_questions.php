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
            // Tambahkan training_type jika belum ada
            if (!Schema::hasColumn('evaluation_questions', 'training_type')) {
                $table->string('training_type')->nullable()->after('id');
            }
            
            // Tambahkan sub_category jika belum ada
            if (!Schema::hasColumn('evaluation_questions', 'sub_category')) {
                $table->string('sub_category')->nullable()->after('category');
            }
            
            // Pastikan kolom options bertipe text/json untuk menyimpan array dropdown
            if (!Schema::hasColumn('evaluation_questions', 'options')) {
                $table->text('options')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_questions', function (Blueprint $table) {
            $table->dropColumn(['training_type', 'sub_category', 'options']);
        });
    }
};
