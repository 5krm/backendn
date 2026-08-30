<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('website')->nullable()->after('description');
            $table->string('category')->nullable()->after('website');
            $table->unsignedSmallInteger('founded')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['website', 'category', 'founded']);
        });
    }
};
