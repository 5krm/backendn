<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });

        DB::statement("UPDATE courses SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE courses SET status = 'published' WHERE status = '1'");
        DB::statement("UPDATE lessons SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE lessons SET status = 'published' WHERE status = '1'");
        DB::statement("UPDATE course_sections SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE course_sections SET status = 'published' WHERE status = '1'");
    }

    public function down(): void
    {
        DB::statement("UPDATE courses SET status = '0' WHERE status = 'draft'");
        DB::statement("UPDATE courses SET status = '1' WHERE status = 'published'");
        DB::statement("UPDATE lessons SET status = '0' WHERE status = 'draft'");
        DB::statement("UPDATE lessons SET status = '1' WHERE status = 'published'");
        DB::statement("UPDATE course_sections SET status = '0' WHERE status = 'draft'");
        DB::statement("UPDATE course_sections SET status = '1' WHERE status = 'published'");

        Schema::table('courses', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });
    }
};
