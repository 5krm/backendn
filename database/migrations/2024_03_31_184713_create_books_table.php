<?php

use App\Enums\BookStatus;
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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('caption');
            $table->text('description');
            $table->string('lang');
            $table->integer('price')->default(0);
            $table->integer('old_price')->default(0);
            $table->string('stripe_price_id')->nullable();
            $table->integer('status')->default(BookStatus::draft->value);
            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::dropIfExists('books');
    }
};
