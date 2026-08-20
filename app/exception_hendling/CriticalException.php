<?php

namespace App\exception_hendling;

class CriticalException extends CustomException
{
    protected string $severity = 'critical';

    protected function getHttpCode(): int
    {
        return 500;
    }
}
