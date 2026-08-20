<?php

namespace App\exception_hendling;

class ValidationException extends CustomException
{
    protected string $severity = 'low';

    protected function getHttpCode(): int
    {
        return 422;
    }
}
