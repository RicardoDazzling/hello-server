<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class CallValidation implements Validate
{
    public static function isCreationSchemaValid(array $data): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data)) return false;
        if(!array_key_exists('audio', $data))
        {
            throw new ValidationException('Missing arguments inside the payload.');
        }
        if(!v::base64()->validate($data['audio']))
        {
            throw new ValidationException('Invalid audio.');
        }
        return true;
    }
}