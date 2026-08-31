<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix courses table - convert ENUM/string to integer
        $this->convertStatusColumn('courses');

        // Fix lessons table - convert string values to integers
        $this->convertStatusColumn('lessons');

        // Fix course_sections table - convert string values to integers
        $this->convertStatusColumn('course_sections');
    }

    private function convertStatusColumn(string $table): void
    {
        $columnType = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'status'", [$table]);

        // Only convert if column is still string/enum type
        if ($columnType && in_array(strtolower($columnType->DATA_TYPE), ['varchar', 'char', 'text', 'enum'])) {
            DB::statement("UPDATE {$table} SET status = 0 WHERE status = 'draft'");
            DB::statement("UPDATE {$table} SET status = 1 WHERE status = 'published'");
        }

        // Always ensure column is INT
        DB::statement("ALTER TABLE {$table} MODIFY COLUMN status INT DEFAULT 0");
    }

    public function down(): void
    {
        // Revert courses
        Schema::table('courses', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
        DB::statement("UPDATE courses SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE courses SET status = 'published' WHERE status = '1'");

        // Revert lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
        DB::statement("UPDATE lessons SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE lessons SET status = 'published' WHERE status = '1'");

        // Revert course_sections
        Schema::table('course_sections', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
        DB::statement("UPDATE course_sections SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE course_sections SET status = 'published' WHERE status = '1'");
    }
};
