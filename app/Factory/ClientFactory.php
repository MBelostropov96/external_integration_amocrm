<?php

declare(strict_types=1);

namespace App\Factory;

use AmoCRM\Client\AmoCRMApiClient;
use App\Exceptions\NotFoundException;
use App\Helper\TokenHelper;
use App\Models\AmoAccount;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class ClientFactory
{
    private AccessTokenFactory $tokenFactory;
    private AmoAccount $account;

    public function __construct(AccessTokenFactory $tokenFactory, AmoAccount $account)
    {
        $this->tokenFactory = $tokenFactory;
        $this->account = $account;
    }

    /**
     * @param string|null $baseDomain
     * @return AmoCRMApiClient
     */
    public function create(string $baseDomain = null): AmoCRMApiClient
    {
        $client = new AmoCRMApiClient(
            env('CLIENT_ID'),
            env('SECRET_KEY'),
            env('BASE_URI')
        );

        if ($baseDomain !== null) {
            $client->setAccountBaseDomain($baseDomain);
        }

        return $client;
    }

    /**
     * @param int $accountId
     * @return AmoCRMApiClient
     * @throws NotFoundException
     */
    public function createByAccountId(int $accountId): AmoCRMApiClient
    {
        try {
            $account = $this->account->findOrFail($accountId);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException('AmoAccount');
        }

        $token = $this->tokenFactory->create($account);

        return $this->createFromToken($account->getBaseDomain(), $token);
    }

    /**
     * @param string $baseDomain
     * @param AccessToken $token
     * @return AmoCRMApiClient
     */
    public function createFromToken(string $baseDomain, AccessToken $token): AmoCRMApiClient
    {
        return $this->create($baseDomain)
            ->setAccessToken($token)
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, $baseDomain) {
                    TokenHelper::updateOrCreateToken($accessToken, $baseDomain);
                }
            );
    }
}
