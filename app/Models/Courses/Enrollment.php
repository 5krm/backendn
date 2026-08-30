<?php

namespace App\Models\Courses;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Enrollment extends Pivot implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'enrollments';

    public $incrementing = true;

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'score',
        'passed_at',
    ];

    protected $casts = [
        'passed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function period(int $duration)
    {
        $now = Carbon::now();
        $required_days = $now->diffInDays($now->copy()->addMinutes($duration)) + 1;

        return [
            'start' => $this->created_at->format('M d, Y'),
            'end' => $this->created_at->addDays($required_days)->format('M d, Y'),
        ];
    }

    /**
     * @return Attribute<string, never>
     */
    protected function certificate(): Attribute
    {
        $file = $this->getMedia('certificates')
            ->last()
            ?->getUrl() ?? null;

        return Attribute::make(
            get: fn () => $file
        );
    }
}
