<?php

namespace DazzRick\HelloServer\Exceptions;

class BadRequestException extends \RuntimeException
{
    public function __construct(string $message = "User token invalid!", int $code = 400, mixed $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}