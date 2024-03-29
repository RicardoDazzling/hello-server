<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class MessageValidation implements Validation
{
    #[\Override] public static function isCreationSchemaValid(array $data, bool $mandatory = true): bool
    {
        if(!BaseValidation::isCreationSchemaValid($data, $mandatory)) return false;
        $content = trim($data['content']);
        if(!v::stringType()->length(1)->validate($content))
        {
            throw new ValidationException('Content is empty');
        }
        return true;
    }
}