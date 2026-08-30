<?php

use App\Models\Courses\Course;
use App\Models\Courses\CourseMail;
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
        Schema::table('course_mail_logs', function (Blueprint $table) {
            $table->foreignIdFor(Course::class)->after('mail_id')->nullable();
            $table->string('type')->after('course_id')->nullable();
            $table->foreignIdFor(CourseMail::class, 'mail_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_mail_logs', function (Blueprint $table) {
            $table->dropColumn('course_id');
            $table->dropColumn('type');
            $table->foreignIdFor(CourseMail::class, 'mail_id')->change();
        });
    }
};
