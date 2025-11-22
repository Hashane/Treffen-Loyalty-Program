<?php

namespace App\Services;

use App\Abstracts\OAuthProvider;

class GoogleOAuthService extends OAuthProvider
{
    protected string $provider = 'google';
}
