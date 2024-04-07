<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class MessageValidation implements Validate
{
    public static function isCreationSchemaValid(array $data): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data)) return false;
        if(!array_key_exists('content', $data))
        {
            throw new ValidationException('Missing arguments inside the payload.');
        }
        $content = trim($data['content']);
        if(!v::stringType()->length(1)->validate($content))
        {
            throw new ValidationException('Content is empty');
        }
        return true;
    }
}