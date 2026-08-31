<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'category_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignIdFor(Category::class)->nullable()->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'category_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }
    }
};
