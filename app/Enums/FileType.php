<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum FileType: int
{
    case pdf = 0;
    case docx = 1;
    case ppt = 2;
    case xlsx = 3;
    case zip = 4;
    case video = 5;
    case image = 6;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


    public static function names(): array
    {
        return [
            static::pdf->value => 'pdf',
            static::docx->value => 'docx',
            static::ppt->value => 'pptx',
            static::xlsx->value => 'xlsx',
            static::zip->value => 'zip',
            static::video->value => 'video',
            static::image->value => 'image',
        ];
    }

    public static function fromMimeType(string $mimeType): FileType
    {
        $matched = match ($mimeType) {
            'application/pdf' => FileType::pdf,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => FileType::docx,
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => FileType::ppt,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => FileType::xlsx,
            'application/zip' => FileType::zip,
            default => null,
        };

        if ($matched) return $matched;

        if (Str::startsWith($mimeType, 'video/')) return FileType::video;
        if (Str::startsWith($mimeType, 'image/')) return FileType::image;

        return FileType::pdf;
    }
}
