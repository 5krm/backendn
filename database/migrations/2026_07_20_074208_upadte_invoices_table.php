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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('amount_subtotal')->default(0);
            $table->renameColumn('amount', 'amount_total')->default(0);
            $table->string('stripe_session_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('amount_subtotal');
            $table->renameColumn('amount_total', 'amount')->default(0);
            $table->dropColumn('stripe_session_id');
        });
    }
};
