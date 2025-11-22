<?php

namespace App\Services;

use App\Abstracts\OAuthProvider;

class GoogleOAuthService extends OAuthProvider
{
    public function __construct()
    {
        parent::__construct('google');
    }
}