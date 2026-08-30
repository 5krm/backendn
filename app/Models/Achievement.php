<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'icon', 'badge_type',
        'criteria_type', 'criteria_value', 'points',
    ];
}
