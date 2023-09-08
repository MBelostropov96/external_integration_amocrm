<?php

declare(strict_types=1);

namespace App\Helper;

use App\Models\AmoAccount;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use League\OAuth2\Client\Token\AccessTokenInterface;

class TokenHelper
{
    /**
     * @param AccessTokenInterface $token
     * @param string $baseDomain
     * @return AmoAccount
     */
    public static function updateOrCreateToken(AccessTokenInterface $token, string $baseDomain): AmoAccount
    {
        // todo Проверить
        $accountId = (new Parser(new JoseEncoder()))->parse((string)$token)->claims()
            ->get('account_id');

        $account = new AmoAccount();
        $account->setAccountId($accountId);
        $account->setBaseDomain($baseDomain);
        $account->setAccessToken($token->getToken());
        $account->setRefreshToken($token->getRefreshToken());
        $account->setExpires($token->getExpires());

        return AmoAccount::updateOrCreate(
            [AmoAccount::ACCOUNT_ID_COLUMN => $accountId],
            $account->toArray(),
        );
    }
}
