<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait CommentScopes
{
    public function scopeParent(Builder $builder): void
    {
        $builder->whereNull('parent_id');
    }
}
