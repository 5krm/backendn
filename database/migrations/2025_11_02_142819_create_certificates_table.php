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
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->foreignId('tutor_id')->constrained()->onDelete('cascade');
                $table->string('certificate_number')->unique();
                $table->timestamp('issued_at');
                $table->timestamp('completed_at');
                $table->decimal('score', 5, 2)->nullable();
                $table->json('template_data')->nullable();
                $table->string('file_path')->nullable();
                $table->boolean('is_valid')->default(true);
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['user_id', 'course_id']);
                $table->index('certificate_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
