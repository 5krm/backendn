<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->timestamp('issued_at')->nullable()->change();
            $table->timestamp('completed_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->timestamp('issued_at')->nullable(false)->change();
            $table->timestamp('completed_at')->nullable(false)->change();
        });
    }
};
