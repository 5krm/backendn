<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_trackings', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_trackings', 'watch_position')) {
                $table->integer('watch_position')->nullable()->default(0)->after('completed_at');
            }
            if (! Schema::hasColumn('lesson_trackings', 'watch_percentage')) {
                $table->float('watch_percentage')->nullable()->default(0)->after('watch_position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lesson_trackings', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('lesson_trackings', 'watch_position')) {
                $columns[] = 'watch_position';
            }
            if (Schema::hasColumn('lesson_trackings', 'watch_percentage')) {
                $columns[] = 'watch_percentage';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
