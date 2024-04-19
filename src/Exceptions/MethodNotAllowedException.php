<?php

namespace DazzRick\HelloServer\Exceptions;

class MethodNotAllowedException extends \RuntimeException
{
    public function __construct(string $message = "Method not allowed!", int $code = 405, mixed $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}