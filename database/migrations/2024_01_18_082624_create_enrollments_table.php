<?php

use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Course::class);
                $table->foreignIdFor(User::class);
                $table->integer('progress')->default(0);
                $table->dateTime('passed_at')->nullable();
                $table->double('score')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
