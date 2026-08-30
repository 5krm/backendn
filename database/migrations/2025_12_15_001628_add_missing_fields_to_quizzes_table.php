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
            if (!Schema::hasColumn('quizzes', 'title')) {
                $table->string('title')->nullable()->after('id');
            }
            if (!Schema::hasColumn('quizzes', 'description')) {
                $table->text('description')->nullable()->after('question');
            }
            if (!Schema::hasColumn('quizzes', 'course_id')) {
                $table->foreignId('course_id')->nullable()->constrained()->after('lesson_id');
            }
            // Note: passing_score and time_limit_minutes removed - all quizzes require 100% with no time limit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('quizzes', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('quizzes', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
        });
    }
};
