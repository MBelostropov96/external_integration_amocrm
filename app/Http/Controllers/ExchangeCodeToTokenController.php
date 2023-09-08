<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factory\ClientFactory;
use App\Http\Requests\ExchangeCodeToTokenRequest;
use App\Jobs\ExchangeCodeToTokenJob;
use Illuminate\Http\Response;

class ExchangeCodeToTokenController extends Controller
{
    private ClientFactory $factory;

    public function __construct(ClientFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param ExchangeCodeToTokenRequest $request
     * @return Response
     */
    public function __invoke(ExchangeCodeToTokenRequest $request): Response
    {
        $data = $request->validated();

        ExchangeCodeToTokenJob::dispatch($data, $this->factory)->onQueue('exchange_token');

        return response()->noContent();
    }
}
