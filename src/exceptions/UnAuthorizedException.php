<?php

namespace DazzRick\HelloServer\Exceptions;

class UnAuthorizedException extends \RuntimeException
{
    public function __construct(string $message = "User token is required!", int $code = 401, mixed $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}