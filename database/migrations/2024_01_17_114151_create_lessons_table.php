<?php

use App\Models\Courses\Course;
use App\Models\Courses\CourseSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->uuid('public_key')->unique();
                $table->integer('lesson_order')->default(1);
                $table->integer('section_order')->default(1);
                $table->integer('order')->virtualAs('section_order * 100 + lesson_order');
                $table->string('title');
                $table->text('content')->nullable();
                $table->string('video_id')->nullable();
                $table->text('video_html')->nullable();
                $table->integer('duration')->default(0);
                $table->foreignIdFor(Course::class);
                $table->foreignIdFor(CourseSection::class, 'section_id');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
