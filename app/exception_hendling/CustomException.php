<?php

namespace App\exception_hendling;

use Exception;
use App\Models\ExceptionLog;

class CustomException extends Exception
{
    protected string $severity = 'medium';

    public function render()
    {
        ExceptionLog::create([
            'message'         => $this->getMessage(),
            'url'             => request()->fullUrl(),
            'exception_type'  => class_basename($this),
            'severity'        => $this->severity,
            'status'          => 'open',
            'stack_trace'     => $this->getTraceAsString(),
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'http_method'     => request()->method(),
            'request_payload' => request()->except(['password', 'password_confirmation']),
        ]);

        return response()->json([
            'status'   => false,
            'message'  => $this->getMessage(),
            'type'     => class_basename($this),
            'severity' => $this->severity,
        ], $this->getHttpCode());
    }

    protected function getHttpCode(): int
    {
        return 400;
    }
}
