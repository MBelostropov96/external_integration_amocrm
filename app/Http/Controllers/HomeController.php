<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factory\ClientFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    private ClientFactory $factory;

    public function __construct(ClientFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return string|RedirectResponse
     */
    public function __invoke(): string|RedirectResponse
    {
        $authorizationUrl = '';

        try {
            $client = $this->factory->create();

            // todo Иногда не записывается state, разобраться
            $state = bin2hex(random_bytes(16));
            session(['state' => $state]);

            if (request()->query->get('button') !== null) {
                return $client->getOAuthClient()->getOAuthButton(
                    [
                        'title' => 'Установить интеграцию',
                        'compact' => true,
                        'class_name' => 'className',
                        'color' => 'default',
                        'error_callback' => 'handleOauthError',
                        'state' => $state,
                    ]
                );
            } else {
                $authorizationUrl = $client->getOAuthClient()->getAuthorizeUrl([
                    'state' => $state,
                    'mode' => 'post_message',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to get authorization link:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }

        return redirect($authorizationUrl);
    }
}
