<?php

namespace App\Actions;

use App\Models\Member;

class CreateMemberAction
{
    /**
     * Create a new member.
     */
    public function execute(array $data): Member
    {
        return Member::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'email_verified_at' => $data['email_verified_at'] ?? null,
            'enrolled_date' => now(),
        ]);
    }

    /**
     * Create a member from OAuth data.
     */
    public function fromOAuth(array $oauthData): Member
    {
        return $this->execute([
            'first_name' => $oauthData['first_name'],
            'last_name' => $oauthData['last_name'],
            'email' => $oauthData['email'],
            'email_verified_at' => now(),
        ]);
    }
}
