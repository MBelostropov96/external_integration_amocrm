<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\InvalidParameterException;
use App\Exceptions\NotFoundException;
use App\Models\AmoAccount;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws InvalidParameterException
     * @throws NotFoundException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accountId = (int)$request->input('account.id');

        if ($accountId <= 0) {
            throw new InvalidParameterException('account_id');
        }

        try {
            AmoAccount::findOrFail($accountId);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundException($e->getMessage());
        }

        $request->merge(['account_id' => $accountId]);

        return $next($request);
    }
}
