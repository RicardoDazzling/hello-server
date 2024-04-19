<?php

namespace DazzRick\HelloServer\Exceptions;

class NotFoundException extends \RuntimeException
{
    public function __construct(string $message = "Request not found", int $code = 404, mixed $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}