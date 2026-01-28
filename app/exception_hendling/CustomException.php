<?php

namespace App\exception_hendling;

use Exception;

class CustomException extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => false,
            'message' => $this->getMessage(),
        ], 400);
    }
}
