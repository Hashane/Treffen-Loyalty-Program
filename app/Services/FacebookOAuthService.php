<?php

namespace App\Services;

use App\Abstracts\OAuthProvider;

class FacebookOAuthService extends OAuthProvider
{
    protected string $provider = 'facebook';
}
