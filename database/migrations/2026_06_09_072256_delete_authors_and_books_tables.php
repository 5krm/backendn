<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });

        Schema::dropIfExists('authors');
        Schema::dropIfExists('book_sections');
        Schema::dropIfExists('books');
    }

    public function down(): void
    {
        //
    }
};
