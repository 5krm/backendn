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
        if (! Schema::hasColumn('tutors', 'user_id')) {
            Schema::table('tutors', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            });
        }

        foreach (['name', 'email', 'password', 'remember_token'] as $legacy) {
            if (Schema::hasColumn('tutors', $legacy)) {
                Schema::table('tutors', function (Blueprint $table) use ($legacy) {
                    $table->dropColumn($legacy);
                });
            }
        }

        $additions = [
            ['experience_years', fn (Blueprint $t) => $t->integer('experience_years')->default(0)->after('specialization')],
            ['website', fn (Blueprint $t) => $t->string('website')->nullable()->after('phone')],
            ['linkedin', fn (Blueprint $t) => $t->string('linkedin')->nullable()->after('website')],
            ['twitter', fn (Blueprint $t) => $t->string('twitter')->nullable()->after('linkedin')],
            ['facebook', fn (Blueprint $t) => $t->string('facebook')->nullable()->after('twitter')],
            ['instagram', fn (Blueprint $t) => $t->string('instagram')->nullable()->after('facebook')],
            ['hourly_rate', fn (Blueprint $t) => $t->decimal('hourly_rate', 8, 2)->nullable()->after('instagram')],
            ['qualifications', fn (Blueprint $t) => $t->json('qualifications')->nullable()->after('hourly_rate')],
            ['languages', fn (Blueprint $t) => $t->json('languages')->nullable()->after('qualifications')],
            ['is_verified', fn (Blueprint $t) => $t->boolean('is_verified')->default(false)->after('is_active')],
            ['verified_at', fn (Blueprint $t) => $t->timestamp('verified_at')->nullable()->after('is_verified')],
        ];

        foreach ($additions as [$column, $adder]) {
            if (! Schema::hasColumn('tutors', $column)) {
                Schema::table('tutors', function (Blueprint $table) use ($adder) {
                    $adder($table);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            'name' => fn (Blueprint $t) => $t->string('name')->after('id'),
            'email' => fn (Blueprint $t) => $t->string('email')->unique()->after('name'),
            'password' => fn (Blueprint $t) => $t->string('password')->after('email'),
            'remember_token' => fn (Blueprint $t) => $t->string('remember_token')->nullable()->after('is_active'),
        ] as $column => $adder) {
            if (! Schema::hasColumn('tutors', $column)) {
                Schema::table('tutors', function (Blueprint $table) use ($adder) {
                    $adder($table);
                });
            }
        }

        if (Schema::hasColumn('tutors', 'user_id')) {
            Schema::table('tutors', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        foreach (['experience_years', 'website', 'linkedin', 'twitter', 'facebook', 'instagram', 'hourly_rate', 'qualifications', 'languages', 'is_verified', 'verified_at'] as $col) {
            if (Schema::hasColumn('tutors', $col)) {
                Schema::table('tutors', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
