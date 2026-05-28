<?php

namespace App\exception_hendling;

use Exception;
use App\Models\ExceptionLog;

class CustomException extends Exception
{
    public function render()
    {
        // Save exception in database
        ExceptionLog::create([

            'message' => $this->getMessage(),

            'url' => request()->fullUrl(),

            'exception_type' => class_basename($this),
        ]);

        // Return JSON response
        return response()->json([

            'status' => false,

            'message' => $this->getMessage(),

            'type' => class_basename($this),

        ], 400);
    }
}