<?php

namespace App\Services;

use App\Abstracts\OAuthProvider;

class FacebookOAuthService extends OAuthProvider
{
    public function __construct()
    {
        parent::__construct('facebook');
    }
}