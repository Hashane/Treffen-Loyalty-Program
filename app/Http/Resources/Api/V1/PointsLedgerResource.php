<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointsLedgerResource extends JsonResource
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
            'points_change' => $this->points_change,
            'points_type' => $this->points_type,
            $this->mergeWhen($request->routeIs('members.points-ledger.show'), [
                'current_points_after' => $this->current_points_after,
                'lifetime_points_after' => $this->lifetime_points_after,
                'expiry_date' => $this->expiry_date,
                'expired' => $this->expired,
                'description' => $this->description,
                'created_at' => $this->created_at,
            ]),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'redemption' => new RedemptionResource($this->whenLoaded('redemption')),

            'adjustment' => $this->when(
                $this->adjusted_by,
                [
                    'adjusted_by_user_id' => $this->adjusted_by,
                    'adjustment_reason' => $this->adjustment_reason,
                ]
            ),
        ];
    }
}
