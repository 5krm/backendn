<?php

use App\Enums\CourseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change column type
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default(CourseStatus::draft->value)->change();
        });

        // update existing integer values to string equivalents
        DB::statement("UPDATE lessons SET status = 'draft' WHERE status = '0'");
        DB::statement("UPDATE lessons SET status = 'published' WHERE status = '1'");
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
        });

        // Convert back to integers
        DB::statement("UPDATE lessons SET status = 0 WHERE status = 'draft'");
        DB::statement("UPDATE lessons SET status = 1 WHERE status = 'published'");
    }
};
