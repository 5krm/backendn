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
        if (! Schema::hasColumn('courses', 'tutor_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('tutor_id')->nullable()->constrained()->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'tutor_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['tutor_id']);
                $table->dropColumn('tutor_id');
            });
        }
    }
};
