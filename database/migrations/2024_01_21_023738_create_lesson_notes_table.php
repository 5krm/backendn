<?php

use App\Models\User;
use App\Models\Lessons\Lesson;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_notes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('note');
            $table->foreignIdFor(Lesson::class);
            $table->foreignIdFor(User::class);
            $table->string('color')->default('#fde68a');;
            $table->integer('seconds')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_notes');
    }
};
