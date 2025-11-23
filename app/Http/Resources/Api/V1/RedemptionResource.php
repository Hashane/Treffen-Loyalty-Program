<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RedemptionResource extends JsonResource
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
            'redemption_code' => $this->redemption_code,
            'points_used' => $this->points_used,
            'qar_amount' => $this->qar_amount,
            'qar_amount_formatted' => 'QAR '.number_format($this->qar_amount, 2),
            'status' => $this->status?->value,

            'reward' => $this->when(
                $this->relationLoaded('reward') && $this->reward,
                [
                    'id' => $this->reward?->id,
                    'name' => $this->reward?->name,
                    'description' => $this->reward?->description,
                    'image_url' => $this->reward?->image_url,
                    'points_required' => $this->reward?->points_required,
                    'qar_value' => $this->reward?->qar_value,
                ]
            ),

            'outlet' => $this->when(
                $this->relationLoaded('outlet') && $this->outlet,
                [
                    'id' => $this->outlet?->id,
                    'name' => $this->outlet?->name,
                    'location' => $this->outlet?->location,
                    'phone' => $this->outlet?->phone,
                ]
            ),

            'initiated_at' => $this->initiated_at?->toIso8601String(),
            'initiated_at_formatted' => $this->initiated_at,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'completed_at_formatted' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancelled_at_formatted' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason,

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
