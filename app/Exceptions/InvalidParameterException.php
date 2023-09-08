<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidParameterException extends IntegrationException
{
    public function __construct(string $parameterName)
    {
        parent::__construct(sprintf('Parameter %s is invalid', $parameterName));
    }
}
