<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('certificates', 'tutor_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->foreignId('tutor_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('certificates', 'tutor_id')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->dropForeign(['tutor_id']);
                $table->dropColumn('tutor_id');
            });
        }
    }
};
