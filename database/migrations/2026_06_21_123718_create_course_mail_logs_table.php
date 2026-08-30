<?php

use App\Models\Courses\CourseMail;
use App\Models\User;
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
        Schema::create('course_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CourseMail::class, 'mail_id');
            $table->foreignIdFor(User::class);
            $table->timestamps();
            // $table->unique(['mail_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_mail_logs');
    }
};
