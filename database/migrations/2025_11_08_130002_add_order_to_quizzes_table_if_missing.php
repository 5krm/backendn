<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('quizzes', 'order')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->integer('order')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('quizzes', 'order')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }
    }
};
