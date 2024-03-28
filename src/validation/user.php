<?php

namespace DazzRick\HelloServer\Validation;

use Respect\Validation\Validator as v;
use DazzRick\HelloServer\Exceptions\ValidationException;

final class UserValidation implements Validation
{
    // storing the min/max lengths for first/last names
    private const int MINIMUM_NAME_LENGTH = 2;
    private const int MAXIMUM_NAME_LENGTH = 80;

    public static function isCreationSchemaValid(array $data, bool $mandatory = true): bool
    {
        // validation schema
        $schemaValidation = v::attribute('name', v::stringType()->length(
            self::MINIMUM_NAME_LENGTH, self::MAXIMUM_NAME_LENGTH), mandatory: $mandatory);
        if (array_key_exists('name', $data))
        {
            throw new ValidationException('Name is empty.');
        }
        if (!$schemaValidation->validate($data))
        {
            throw new ValidationException(sprintf('Name is out of range (%d, %d).',
                self::MINIMUM_NAME_LENGTH, self::MAXIMUM_NAME_LENGTH));
        }

        $schemaValidation = $schemaValidation->attribute('email', v::email(), mandatory: $mandatory);
        if (!$schemaValidation->validate($data))
        {
            throw new ValidationException('Invalid Email.');
        }

        $schemaValidation = $schemaValidation->attribute('phone', v::phone(), mandatory: $mandatory);
        if (!$schemaValidation->validate($data))
        {
            throw new ValidationException('Invalid Phone.');
        }

        return true;
    }

    public static function isUpdateSchemaValid(array $data): bool
    {
        return self::isCreationSchemaValid($data, false);
    }
}