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
        if (! Schema::hasTable('tutors')) {
            Schema::create('tutors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('bio')->nullable();
                $table->string('specialization')->nullable();
                $table->integer('experience_years')->default(0);
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('twitter')->nullable();
                $table->string('facebook')->nullable();
                $table->string('instagram')->nullable();
                $table->decimal('hourly_rate', 8, 2)->nullable();
                $table->json('qualifications')->nullable();
                $table->json('languages')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_verified')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
