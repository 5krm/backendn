<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('verification_code', 12)->nullable()->unique()->after('certificate_number');
            $table->string('status')->default('valid')->after('is_valid');
            $table->index('status');
        });

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        DB::table('certificates')->orderBy('id')->chunkById(100, function ($certificates) use ($alphabet) {
            foreach ($certificates as $certificate) {
                do {
                    $code = '';
                    for ($i = 0; $i < 12; $i++) {
                        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
                    }
                } while (DB::table('certificates')->where('verification_code', $code)->exists());

                DB::table('certificates')->where('id', $certificate->id)->update([
                    'verification_code' => $code,
                    'status' => ($certificate->is_valid ?? true) ? 'valid' : 'revoked',
                ]);
            }
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->string('verification_code', 12)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropUnique(['verification_code']);
            $table->dropColumn(['verification_code', 'status']);
        });
    }
};
