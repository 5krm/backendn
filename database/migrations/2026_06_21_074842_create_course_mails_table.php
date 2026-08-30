<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Courses\Course;
use App\Models\User;
use App\Enums\CourseEmailType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_mails', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class);
            $table->string('subject');
            $table->text('body');
            $table->string('type')->default(CourseEmailType::welcome->value);
            $table->boolean('active')->default(true);
            $table->foreignIdFor(User::class, 'created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_mails');
    }
};
