<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add extra fields to achievements
        Schema::table('achievements', function (Blueprint $table) {
            if (! Schema::hasColumn('achievements', 'badge_type')) {
                $table->string('badge_type')->default('milestone')->after('icon');
            }
            if (! Schema::hasColumn('achievements', 'criteria_type')) {
                $table->string('criteria_type')->nullable()->after('badge_type');
            }
            if (! Schema::hasColumn('achievements', 'criteria_value')) {
                $table->unsignedInteger('criteria_value')->default(1)->after('criteria_type');
            }
        });

        // Add body and image_url to feeds (it uses 'content' col, add body as alias)
        Schema::table('feeds', function (Blueprint $table) {
            if (! Schema::hasColumn('feeds', 'body')) {
                $table->text('body')->nullable()->after('content');
            }
            if (! Schema::hasColumn('feeds', 'image_url')) {
                $table->string('image_url')->nullable()->after('body');
            }
        });

        // Add content_type to static_pages
        Schema::table('static_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('static_pages', 'content_type')) {
                $table->string('content_type')->default('html')->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropColumn(['badge_type', 'criteria_type', 'criteria_value']);
        });
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn(['body', 'image_url']);
        });
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
};
