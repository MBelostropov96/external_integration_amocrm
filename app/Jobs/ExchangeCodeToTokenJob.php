<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\InvalidParameterException;
use App\Factory\ClientFactory;
use App\Helper\TokenHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExchangeCodeToTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $message;
    private ClientFactory $factory;

    public function __construct(array $message, ClientFactory $factory)
    {
        $this->message = $message;
        $this->factory = $factory;
    }

    public function handle(): void
    {
        // todo @see HomeController:30
//        if ($this->message['state'] !== session('state')) {
//            Log::error('State parameters do not match:', [$this->message['state'], session('state')]);
//            throw new InvalidParameterException('state');
//        }
//        session()->forget('state');

        Log::info('Exchange code to token start:', $this->message);

        try {
            $client = $this->factory->create($this->message['referer']);
            $token = $client->getOAuthClient()->getAccessTokenByCode($this->message['code']);

            $amoAccount = TokenHelper::updateOrCreateToken($token, $this->message['referer']);

            Log::info('Added token for account: ' . $amoAccount->getAccountId(), $amoAccount->jsonSerialize());
        } catch (\Throwable $e) {
            Log::error('Failed to add token:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
    }
}
