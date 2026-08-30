<?php

namespace App\Models\Lessons;

use App\Enums\FileType;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonResource extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'public_key',
        'title',
        'file_type',
        'file_path',
        'lesson_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (LessonResource $resource) {
            if (empty($resource->public_key)) {
                $resource->public_key = (string) Str::uuid();
            }

            if (is_null($resource->file_type) && $resource->file_path) {
                $ext = strtolower(pathinfo($resource->file_path, PATHINFO_EXTENSION));
                $resource->file_type = match ($ext) {
                    'pdf'                   => FileType::pdf,
                    'doc', 'docx'           => FileType::docx,
                    'ppt', 'pptx'           => FileType::ppt,
                    'xls', 'xlsx'           => FileType::xlsx,
                    'zip'                   => FileType::zip,
                    'mp4', 'avi', 'mov', 'wmv' => FileType::video,
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp' => FileType::image,
                    default                 => FileType::pdf,
                };
            }

            $resource->file_type ??= FileType::pdf;
        });
    }

    protected $casts = [
        'file_type' => FileType::class,
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * @return Attribute<string, never>
     */
    protected function file(): Attribute
    {
        $file = $this->getMedia('resources')
            ->last()
            ?->getUrl();

        return Attribute::make(
            get: fn () => $file
        );
    }
}
