<?php

use App\Enums\CourseStatus;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Project migrations are MySQL-specific; phpunit.xml defaults to sqlite :memory:.
    config([
        'database.default' => 'mysql',
        'database.connections.mysql.host' => env('DB_HOST', 'mysql'),
        'database.connections.mysql.port' => env('DB_PORT', '3306'),
        'database.connections.mysql.database' => 'laravel',
        'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
        'database.connections.mysql.password' => env('DB_PASSWORD', ''),
    ]);

    DB::purge('mysql');
    DB::reconnect('mysql');
});

function createCertificateRecord(array $overrides = []): Certificate
{
    $student = User::factory()->create();
    $tutor = User::factory()->create();

    $course = new Course;
    $course->forceFill([
        'title' => 'Laravel Masterclass',
        'slug' => 'laravel-masterclass-'.Str::random(6),
        'description' => 'Test course description',
        'tutor_id' => $tutor->id,
        'status' => CourseStatus::published,
        'lang' => 'en',
        'price' => 0,
        'old_price' => 0,
        'duration' => 60,
        'order' => 1,
    ]);
    $course->save();
    $course->setRelation('tutor', $tutor);

    $certificate = app(CertificateService::class)->issueCertificate($student, $course, 95);

    $certificate->forceFill(array_merge([
        'issued_at' => now()->subDays(2),
        'completed_at' => now()->subDays(4),
        'template_data' => [
            'course_title' => 'Laravel Masterclass',
            'student_name' => $student->name,
            'completion_date' => now()->subDays(4)->format('Y-m-d'),
            'score' => 95,
        ],
    ], $overrides))->save();

    return $certificate->fresh(['user', 'course']);
}

it('assigns CERT-style certificate number and verification code', function () {
    $certificate = createCertificateRecord();

    expect($certificate->certificate_number)
        ->toStartWith('CERT-'.date('Y').'-')
        ->and($certificate->verification_code)->toHaveLength(12)
        ->and($certificate->status)->toBe(Certificate::STATUS_VALID);
});

it('shows a valid verification page with completion and issue dates', function () {
    $certificate = createCertificateRecord();

    $response = $this->get(route('certificates.verify', $certificate->verification_code));

    $response->assertSuccessful()
        ->assertSee(__('certificates.verified'), false)
        ->assertSee($certificate->user->name, false)
        ->assertSee('Laravel Masterclass', false)
        ->assertSee($certificate->certificate_number, false)
        ->assertSee(__('certificates.completed'), false)
        ->assertSee(__('certificates.issued'), false)
        ->assertSee(__('certificates.download'), false)
        ->assertSee(route('certificate', $certificate), false);
});

it('hides download and blocks pdf for revoked certificates', function () {
    $certificate = createCertificateRecord([
        'status' => Certificate::STATUS_REVOKED,
    ]);

    $this->get(route('certificates.verify', $certificate->verification_code))
        ->assertSuccessful()
        ->assertSee(__('certificates.not_valid'), false)
        ->assertSee(__('certificates.revoked_message'), false)
        ->assertDontSee(__('certificates.download'), false);

    $this->get(route('certificate', $certificate))->assertNotFound();
});

it('shows not found for unknown verification codes', function () {
    $this->get(route('certificates.verify', 'UNKNOWNCODE12'))
        ->assertSuccessful()
        ->assertSee(__('certificates.not_found_message'), false);
});

it('builds linkedin urls with verification url and credential id', function () {
    $certificate = createCertificateRecord();

    $addUrl = $certificate->addToLinkedin();
    $shareUrl = $certificate->shareLink();

    expect($addUrl)
        ->toContain(urlencode($certificate->verificationUrl()))
        ->toContain('certId='.urlencode($certificate->certificate_number))
        ->and($shareUrl)->toContain(urlencode($certificate->verificationUrl()));
});

it('issues certificates through the service with both identifiers', function () {
    $student = User::factory()->create();
    $tutor = User::factory()->create();

    $course = new Course;
    $course->forceFill([
        'title' => 'Service Issued Course',
        'slug' => 'service-issued-'.Str::random(6),
        'description' => 'Test course description',
        'tutor_id' => $tutor->id,
        'status' => CourseStatus::published,
        'lang' => 'en',
        'price' => 0,
        'old_price' => 0,
        'duration' => 60,
        'order' => 1,
    ]);
    $course->save();
    $course->setRelation('tutor', $tutor);

    $certificate = app(CertificateService::class)->issueCertificate($student, $course, 88.5);

    expect($certificate->verification_code)->toHaveLength(12)
        ->and($certificate->certificate_number)->toStartWith('CERT-'.date('Y').'-')
        ->and($certificate->status)->toBe(Certificate::STATUS_VALID);
});
