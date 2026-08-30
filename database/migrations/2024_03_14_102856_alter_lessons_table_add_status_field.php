<?php

use App\Enums\CourseStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('lessons', 'status')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->string('status')->default(CourseStatus::draft->value);
            });
            DB::statement("UPDATE lessons SET status = 'published'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lessons', 'status')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
