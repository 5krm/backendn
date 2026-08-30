<?php

namespace App\Data\Profile;

use App\Enums\Language;
use Spatie\LaravelData\Data;

class DisplaySettingData extends Data
{
  public function __construct(
    public Language $displayLanguage,
    public Language $learningLanguage,
  ) {
  }
}
