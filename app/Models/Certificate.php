<?php

namespace App\Models;

use App\Models\Courses\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public const STATUS_VALID = 'valid';

    public const STATUS_REVOKED = 'revoked';

    public const STATUS_EXPIRED = 'expired';

    protected $guarded = ['id'];

    protected $casts = [
        'issued_at' => 'datetime',
        'completed_at' => 'datetime',
        'template_data' => 'array',
        'score' => 'float',
        'is_valid' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function isValid(): bool
    {
        return $this->status === self::STATUS_VALID && ! $this->trashed();
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function verificationUrl(): string
    {
        return route('certificates.verify', ['code' => $this->verification_code]);
    }

    public function getDownloadUrlAttribute(): ?string
    {
        return $this->getMedia('certificates')->last()?->getUrl();
    }

    public function shareLink(): string
    {
        return 'https://www.linkedin.com/sharing/share-offsite/?url='.urlencode($this->verificationUrl());
    }

    public function addToLinkedin(): string
    {
        $course = Course::query()->findOrFail($this->course_id);
        $certificate = Certificate::query()
            ->where('user_id', $this->user_id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $organizationId = '75645939';
        $certificationName = $course->title;
        $issueYear = $certificate->issued_at?->year;
        $issueMonth = $certificate->issued_at?->month;
        $certUrl = $certificate->verificationUrl();
        $skills = $course->category?->localizedName ?? '';

        $linkedInAddUrl =
            'https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME'.
            '&name='.
            urlencode($certificationName).
            '&organizationId='.
            urlencode($organizationId).
            '&issueYear='.
            $issueYear.
            '&issueMonth='.
            $issueMonth.
            '&certUrl='.
            urlencode($certUrl).
            '&certId='.
            urlencode($certificate->certificate_number).
            ($skills ? '&skills='.urlencode($skills) : '');

        return $linkedInAddUrl;
    }
}
