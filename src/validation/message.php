<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

final class MessageValidation implements Validation
{
    #[\Override] public static function isCreationSchemaValid(array $data, bool $mandatory = true): bool
    {
        if(!(array_key_exists('to', $data) && array_key_exists('content', $data)))
        {
            throw new ValidationException('Missing arguments inside the payload.');
        }
        if(!v::uuid()->validate($data['to']))
        {
            throw new ValidationException('Invalid receiver uuid');
        }
        if(UserDAL::get($data['to'])->isEmpty())
        {
            throw new ValidationException("Receiver doesn't exist.");
        }
        $content = trim($data['content']);
        if(!v::stringType()->length(1)->validate($content))
        {
            throw new ValidationException('Content is empty');
        }
        return true;
    }
}