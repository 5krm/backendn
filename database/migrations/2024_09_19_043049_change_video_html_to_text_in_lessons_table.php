<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('lessons', 'video_html')) {
            DB::statement('ALTER TABLE lessons MODIFY video_html TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lessons', 'video_html')) {
            DB::statement('ALTER TABLE lessons MODIFY video_html VARCHAR(255) NULL');
        }
    }
};
