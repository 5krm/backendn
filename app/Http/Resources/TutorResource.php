<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;

        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'job_title' => $user?->localized_job_title ?? '',
            'bio' => $user?->localized_bio,
            'image' => $this->profile_image,
            'courses_count' => $this->courses_count ?? 0,
            'slug' => '',
            'social_links' => $user?->socialLinks?->map(fn ($link) => [
                'platform' => $link->platform?->value ?? ($link->getAttributes()['platform'] ?? null),
                'url' => $link->url,
            ])->values() ?? [],
        ];
    }
}
