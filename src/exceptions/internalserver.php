<?php

namespace DazzRick\HelloServer\Exceptions;

class InternalServerException extends \RuntimeException
{
    public function __construct(string $message = "Internal server error", int $code = 500, mixed $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}