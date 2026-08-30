<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }
}
