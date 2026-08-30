<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('certificates')
            ->where('certificate_number', 'like', 'P365-%')
            ->orderBy('id')
            ->chunkById(100, function ($certificates) {
                foreach ($certificates as $certificate) {
                    $year = $certificate->issued_at
                        ? date('Y', strtotime($certificate->issued_at))
                        : date('Y', strtotime($certificate->created_at ?? 'now'));

                    do {
                        $number = 'CERT-' . $year . '-' . strtoupper(uniqid());
                    } while (DB::table('certificates')->where('certificate_number', $number)->exists());

                    DB::table('certificates')->where('id', $certificate->id)->update([
                        'certificate_number' => $number,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Irreversible: original CERT numbers overwritten by P365 were not retained.
    }
};
