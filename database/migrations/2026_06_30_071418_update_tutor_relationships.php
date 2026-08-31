<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::table("courses", function (Blueprint $table) {
    //         $table->dropForeign("courses_tutor_id_foreign");
    //         $table->dropIndex("courses_tutor_id_foreign");
    //         DB::update("update courses inner join tutors on courses.tutor_id = tutors.id set courses.tutor_id = tutors.user_id;");
    //         $table->foreignId('tutor_id')->nullable()->change()->constrained("users")->cascadeOnDelete();
    //     });

    //     Schema::table("lessons", function (Blueprint $table) {
    //         $table->dropForeign("lessons_tutor_id_foreign");
    //         $table->dropIndex("lessons_tutor_id_foreign");
    //         DB::update("update lessons inner join tutors on lessons.tutor_id = tutors.id set lessons.tutor_id = tutors.user_id;");
    //         $table->foreignId('tutor_id')->nullable()->change()->constrained("users")->cascadeOnDelete();
    //     });

    //     Schema::table("quizzes", function (Blueprint $table) {
    //         $table->dropForeign("quizzes_tutor_id_foreign");
    //         $table->dropIndex("quizzes_tutor_id_foreign");
    //         DB::update("update quizzes inner join tutors on quizzes.tutor_id = tutors.id set quizzes.tutor_id = tutors.user_id;");
    //         $table->foreignId('tutor_id')->nullable()->change()->constrained("users")->cascadeOnDelete();
    //     });

    //     Schema::table("certificates", function (Blueprint $table) {
    //         $table->dropForeign("certificates_tutor_id_foreign");
    //         $table->dropIndex("certificates_tutor_id_foreign");
    //         DB::update("update certificates inner join tutors on certificates.tutor_id = tutors.id set certificates.tutor_id = tutors.user_id;");
    //         $table->foreignId('tutor_id')->nullable()->change()->constrained("users")->cascadeOnDelete();
    //     });
    // }
    public function up(): void
    {
        // 1. Drop constraints first
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });

        // 2. Now safe to update data
        DB::statement('
            UPDATE courses
            INNER JOIN tutors ON courses.tutor_id = tutors.id
            SET courses.tutor_id = tutors.user_id
        ');

        DB::statement('
            UPDATE lessons
            INNER JOIN tutors ON lessons.tutor_id = tutors.id
            SET lessons.tutor_id = tutors.user_id
        ');

        DB::statement('
            UPDATE quizzes
            INNER JOIN tutors ON quizzes.tutor_id = tutors.id
            SET quizzes.tutor_id = tutors.user_id
        ');

        DB::statement('
            UPDATE certificates
            INNER JOIN tutors ON certificates.tutor_id = tutors.id
            SET certificates.tutor_id = tutors.user_id
        ');

        // 3. Re-add foreign keys to users table
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('tutor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('tutor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreign('tutor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('tutor_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
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
