<?php

declare(strict_types=1);

namespace App\Factory;

use App\Models\AmoAccount;
use League\OAuth2\Client\Token\AccessToken;

class AccessTokenFactory
{
    /**
     * @param AmoAccount $account
     * @return AccessToken
     */
    public function create(AmoAccount $account): AccessToken
    {
        return new AccessToken([
            'access_token' => $account->getAccessToken(),
            'refresh_token' => $account->getRefreshToken(),
            'expires' => $account->getExpires(),
        ]);
    }
}
