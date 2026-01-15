<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'member_number' => $this->member_number,
            'referral_code' => $this->referral_code,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'initials' => $this->initials,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'avatar_url' => '',

            'id_type' => $this->id_type,
            'qatar_id_or_passport' => $this->qatar_id_or_passport,

            'email_verified' => (bool) $this->email_verified_at,
            'account_status' => $this->status,

            'points' => [
                'current' => $this->current_points,
                'lifetime' => $this->lifetime_points,
            ],
            'membership_tier' => new MembershipTierResource($this->whenLoaded('membershipTier')),

            'preferred_communication' => $this->preferred_communication,

            'qr_code_url' => $this->when(
                $this->qr_code_path,
                fn () => asset('storage/'.$this->qr_code_path)
            ),

            'connected_accounts' => OAuthConnectionResource::collection($this->whenLoaded('oauthConnections')),

            'enrolled_date' => $this->enrolled_date?->format('Y-m-d'),
            'member_since' => $this->enrolled_date?->diffForHumans(),
        ];
    }
}
