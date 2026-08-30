<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ListItemData extends Data
{
  public function __construct(
    public string $value,
    public string $text
  ) {
  }
}
