<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasPrimary = DB::table(DB::raw('information_schema.KEY_COLUMN_USAGE'))
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'courses')
            ->where('COLUMN_NAME', 'id')
            ->where('CONSTRAINT_NAME', 'PRIMARY')
            ->exists();

        $hasAutoIncrement = DB::table(DB::raw('information_schema.COLUMNS'))
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'courses')
            ->where('COLUMN_NAME', 'id')
            ->where('EXTRA', 'like', '%auto_increment%')
            ->exists();

        if (!$hasAutoIncrement && !$hasPrimary) {
            DB::statement('ALTER TABLE courses MODIFY id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY');
        } elseif (!$hasAutoIncrement && $hasPrimary) {
            DB::statement('ALTER TABLE courses MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
        } elseif ($hasAutoIncrement && !$hasPrimary) {
            DB::statement('ALTER TABLE courses ADD PRIMARY KEY (id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasAutoIncrement = DB::table(DB::raw('information_schema.COLUMNS'))
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'courses')
            ->where('COLUMN_NAME', 'id')
            ->where('EXTRA', 'like', '%auto_increment%')
            ->exists();

        if ($hasAutoIncrement) {
            DB::statement('ALTER TABLE courses MODIFY id BIGINT UNSIGNED');
        }
    }
};
