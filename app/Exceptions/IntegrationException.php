<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class IntegrationException extends Exception
{
    public function __construct(string $parameterName)
    {
        parent::__construct(sprintf('Integration error: %s', $parameterName));
    }
}
