<?php

namespace App\Enums;

use App\Data\ListItemData;
use Illuminate\Support\Collection;

enum Language: string
{
    case English = 'en';
    case Arabic = 'ar';

    public static function getListItems(): Collection
    {
        return collect(self::cases())->map(fn ($value) => new ListItemData($value->value, $value->name));
    }
}
