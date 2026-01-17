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
            'current_points' => $this->current_points,
            'lifetime_points' => $this->lifetime_points,
            'tier_progress' => $this->next_tier ? [
                'next_tier' => strtoupper($this->next_tier->tier_name),
                'next_tier_points' => $this->next_tier_points,
                'points_to_next_tier' => $this->points_to_next_tier,
                'progress_percentage' => $this->next_tier_points > 0
                    ? round((($this->next_tier_points - $this->points_to_next_tier) / $this->next_tier_points) * 100, 1)
                    : 0,
            ] : null,
            'avatar_url' => '', // TODO: Implement avatar upload/storage
            'initials' => $this->initials,
        ];
    }
}
