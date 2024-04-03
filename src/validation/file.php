<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class FileValidation implements Validation
{
    #[\Override] public static function isCreationSchemaValid(array $data): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data)) return false;
        if(!array_key_exists('content', $data))
        {
            throw new ValidationException('Missing arguments inside the payload.');
        }
        $content = trim($data['content']);
        if(!v::base64()->length(1)->validate($content))
        {
            throw new ValidationException('Content is empty or is not a base64');
        }
        return true;
    }
}