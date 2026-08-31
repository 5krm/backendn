<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the id column to be auto-increment (primary key already exists)
        DB::statement('ALTER TABLE media MODIFY id BIGINT UNSIGNED AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the id column to not auto-increment (though this is rarely needed)
        DB::statement('ALTER TABLE media MODIFY id BIGINT UNSIGNED');
    }
};
