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
            self::happy->value => 'happy',
            self::satisfied->value => 'satisfied',
            self::neutral->value => 'neutral',
            self::unsatisfied->value => 'unsatisfied',
            self::angry->value => 'angry',
        ];
    }
}
