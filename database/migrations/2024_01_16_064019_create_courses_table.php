<?php

use App\Enums\CourseStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->uuid('slug')->unique()->nullable();
                $table->integer('duration')->default(0); //minutes
                $table->integer('order')->default(1);
                $table->string('title');
                $table->text('description');
                $table->text('objectives')->nullable();
                $table->string('lang');
                $table->integer('old_price')->default(0);
                $table->integer('price')->default(0);
                $table->string('stripe_price_id')->nullable();
                $table->integer('status')->default(CourseStatus::draft->value);
                $table->timestamps();
                $table->softDeletesTz();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
