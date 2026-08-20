<?php

namespace App\exception_hendling;

class NotFoundException extends CustomException
{
    protected string $severity = 'medium';

    protected function getHttpCode(): int
    {
        return 404;
    }
}
