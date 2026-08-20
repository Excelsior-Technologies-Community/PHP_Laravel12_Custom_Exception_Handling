<?php

namespace App\exception_hendling;

class AuthenticationException extends CustomException
{
    protected string $severity = 'high';

    protected function getHttpCode(): int
    {
        return 401;
    }
}
