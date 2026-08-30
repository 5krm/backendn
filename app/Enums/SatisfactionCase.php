<?php

namespace App\Enums;

enum SatisfactionCase: int
{
    case happy = 5;
    case satisfied = 4;
    case neutral = 3;
    case unsatisfied = 2;
    case angry = 1;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


    public static function names(): array
    {
        return [
            static::happy->value => 'happy',
            static::satisfied->value => 'satisfied',
            static::neutral->value => 'neutral',
            static::unsatisfied->value => 'unsatisfied',
            static::angry->value => 'angry',
        ];
    }
}
