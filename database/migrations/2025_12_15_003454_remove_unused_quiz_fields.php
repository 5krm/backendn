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
        Schema::table('quizzes', function (Blueprint $table) {
            // Remove passing_score and time_limit_minutes as they're no longer needed
            // All quizzes now require 100% and have no time limit
            if (Schema::hasColumn('quizzes', 'passing_score')) {
                $table->dropColumn('passing_score');
            }
            if (Schema::hasColumn('quizzes', 'time_limit_minutes')) {
                $table->dropColumn('time_limit_minutes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Add them back if needed
            $table->integer('passing_score')->default(100)->after('tutor_id');
            $table->integer('time_limit_minutes')->nullable()->after('passing_score');
        });
    }
};
