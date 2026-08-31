<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'slug')) {
            try {
                DB::statement('ALTER TABLE courses MODIFY slug VARCHAR(255) NULL');
                DB::statement('ALTER TABLE courses ADD UNIQUE INDEX courses_slug_unique (slug)');
            } catch (Throwable $e) {
                // Skip if index already exists or DBAL not available; migration considered applied
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'slug')) {
            try {
                DB::statement('ALTER TABLE courses MODIFY slug CHAR(36) NULL');
            } catch (Throwable $e) {
                // noop
            }
        }
    }
};
