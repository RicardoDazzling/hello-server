<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;

final class LostValidation implements Validation
{
    #[\Override] public static function isCreationSchemaValid(array $data): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data)) return false;
        if(!array_key_exists('type', $data))
        {
            throw new ValidationException('Missing arguments inside the payload.');
        }
        if(in_array($data['type'], ['audio', 'video'], TRUE))
        {
            throw new ValidationException('Invalid typé.');
        }
        return true;
    }
}