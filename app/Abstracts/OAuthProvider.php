<?php

namespace App\Abstracts;

use App\Actions\CreateMemberAction;
use App\Models\Member;
use App\Models\OauthConnection;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

abstract class OAuthProvider
{
    protected string $provider;

    public function __construct(
        string $provider,
        protected CreateMemberAction $createMember
    ) {
        $this->provider = $provider;
    }

    public function getRedirectUrl(): string
    {
        return Socialite::driver($this->provider)
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    public function handleCallback(): array
    {
        try {
            $oauthUser = Socialite::driver($this->provider)
                ->stateless()
                ->user();

            if (! $oauthUser->getEmail()) {
                throw new \Exception('Email not provided by '.$this->provider);
            }

            return DB::transaction(function () use ($oauthUser) {
                return $this->processOAuthUser($oauthUser);
            });

        } catch (\Exception $e) {
            \Log::error("OAuth {$this->provider} callback failed", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function processOAuthUser(SocialiteUser $oauthUser): array
    {
        $connection = OauthConnection::where('provider', $this->provider)
            ->where('provider_id', $oauthUser->getId())
            ->first();

        if ($connection) {
            $connection->update([
                'provider_token' => $oauthUser->token,
                'provider_refresh_token' => $oauthUser->refreshToken,
                'avatar' => $oauthUser->getAvatar(),
            ]);

            $member = $connection->member;
        } else {
            $member = Member::where('email', $oauthUser->getEmail())->first();

            if (! $member) {
                $nameParts = $this->parseFullName($oauthUser->getName());

                $member = $this->createMember->fromOAuth([
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $oauthUser->getEmail(),
                ]);
            }

            $member->oauthConnections()->create([
                'provider' => $this->provider,
                'provider_id' => $oauthUser->getId(),
                'provider_token' => $oauthUser->token,
                'provider_refresh_token' => $oauthUser->refreshToken,
                'avatar' => $oauthUser->getAvatar(),
            ]);
        }

        $token = $member->createToken("{$this->provider}-auth")->plainTextToken;

        return [
            'member' => $member->load('membershipTier'),
            'token' => $token,
        ];
    }

    protected function parseFullName(string $fullName): array
    {
        $parts = array_filter(explode(' ', trim($fullName)));

        if (count($parts) === 1) {
            return [
                'first_name' => $parts[0],
                'last_name' => $parts[0],
            ];
        }

        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }
}
