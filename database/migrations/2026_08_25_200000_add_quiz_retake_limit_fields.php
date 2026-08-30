<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_trackings', function (Blueprint $table) {
            $table->integer('attempts_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->integer('retake_limit')->nullable()->default(3);
            $table->integer('cooldown_minutes')->nullable()->default(1440); // 24 hours
            $table->integer('pass_percent')->nullable()->default(100);
        });
    }

    public function down(): void
    {
        Schema::table('lesson_trackings', function (Blueprint $table) {
            $table->dropColumn(['attempts_count', 'last_attempt_at']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['retake_limit', 'cooldown_minutes', 'pass_percent']);
        });
    }
};
