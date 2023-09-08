<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\InvalidParameterException;
use App\Models\Entity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws InvalidParameterException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $entityType = array_intersect_key(array_flip(Entity::availableEntities()), $request->post());

        if (empty($entityType)) {
            throw new InvalidParameterException('entity');
        }

        $request->merge(['entity_type' => array_key_first($entityType)]);

        return $next($request);
    }
}
