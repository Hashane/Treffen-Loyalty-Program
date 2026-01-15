<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array for home screen.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->member_number,
            'name' => $this->full_name,
            'email' => $this->email,
            'tier' => strtoupper($this->membershipTier->tier_name),
            'points' => $this->current_points,
            'avatar_url' => '', // TODO: Implement avatar upload/storage
            'initials' => $this->initials,
        ];
    }
}
