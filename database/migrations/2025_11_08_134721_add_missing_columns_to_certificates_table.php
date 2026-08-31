<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('issued_at');
            }
            if (! Schema::hasColumn('certificates', 'score')) {
                $table->decimal('score', 5, 2)->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('certificates', 'template_data')) {
                $table->json('template_data')->nullable()->after('score');
            }
            if (! Schema::hasColumn('certificates', 'file_path')) {
                $table->string('file_path')->nullable()->after('template_data');
            }
            if (! Schema::hasColumn('certificates', 'is_valid')) {
                $table->boolean('is_valid')->default(true)->after('file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('certificates', 'completed_at')) {
                $columns[] = 'completed_at';
            }
            if (Schema::hasColumn('certificates', 'score')) {
                $columns[] = 'score';
            }
            if (Schema::hasColumn('certificates', 'template_data')) {
                $columns[] = 'template_data';
            }
            if (Schema::hasColumn('certificates', 'file_path')) {
                $columns[] = 'file_path';
            }
            if (Schema::hasColumn('certificates', 'is_valid')) {
                $columns[] = 'is_valid';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
