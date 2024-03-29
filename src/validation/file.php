<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class FileValidation implements Validation
{
    #[\Override] public static function isCreationSchemaValid(array $data, bool $mandatory = true): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data, $mandatory)) return false;
        $content = trim($data['content']);
        if(!v::base64()->length(1)->validate($content))
        {
            throw new ValidationException('Content is empty or is not a base64');
        }
        return true;
    }
}