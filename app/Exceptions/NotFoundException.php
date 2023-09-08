<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends IntegrationException
{
    public function __construct(string $parameterName)
    {
        parent::__construct($parameterName);
    }
}
