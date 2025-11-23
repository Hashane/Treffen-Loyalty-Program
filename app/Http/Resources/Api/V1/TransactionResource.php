<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'check_number' => $this->check_number,
            'guest_name' => $this->guest_name,
            'department' => $this->department,
            'transaction_date' => $this->transaction_date,
            'booking_reference' => $this->booking_reference,
            'hotel_property' => $this->hotel_property,
            'total_amount' => $this->total_amount,
            'points_earned' => $this->points_earned,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
