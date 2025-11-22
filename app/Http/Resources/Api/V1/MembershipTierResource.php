<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipTierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tier_name' => $this->tier_name,
            'tier_level' => $this->tier_level,
            'points_threshold' => $this->points_threshold,
            'points_multiplier' => $this->points_multiplier,
            'benefits' => $this->benefits,
            'is_active' => $this->is_active,
        ];
    }
}
