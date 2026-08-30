<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'content', 'body', 'image_url', 'type', 'is_published'];
}
