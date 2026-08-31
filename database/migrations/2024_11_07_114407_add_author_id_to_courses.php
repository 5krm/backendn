<?php

use App\Models\Author;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('courses', 'author_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignIdFor(Author::class)->nullable()->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('courses', 'author_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('author_id');
            });
        }
    }
};
