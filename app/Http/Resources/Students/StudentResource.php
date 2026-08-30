<?php

namespace App\Http\Resources\Students;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'media' => $this->profile,
            'verified_email' => $this->email_verified_at?->format('M d, Y'),
            'joined' => $this->created_at?->format('M d, Y'),
            'notes_count' => $this->notes_count,
            'comments_count' => $this->comments_count,
        ];
    }
}
