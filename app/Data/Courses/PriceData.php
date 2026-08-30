<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;

class PriceData extends Data
{
    public function __construct(
        public int $old_price,
        public int $price,
        public bool $is_free,
        public ?string $stripe_price_id,
    ) {}
}
