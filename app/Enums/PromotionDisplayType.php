<?php

namespace App\Enums;

enum PromotionDisplayType: string
{
    case Template = 'template';
    case Image = 'image';

    public function label(): string
    {
        return match ($this) {
            self::Template => __('tutor.promotions.display_type_template'),
            self::Image => __('tutor.promotions.display_type_image'),
        };
    }
}
