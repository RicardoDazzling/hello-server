<?php

namespace DazzRick\HelloServer\Validation;

use Respect\Validation\Validator as v;
use DazzRick\HelloServer\Exceptions\ValidationException;

class UserValidation
{
    // storing the min/max lengths for first/last names
    private const int MINIMUM_NAME_LENGTH = 2;
    private const int MAXIMUM_NAME_LENGTH = 80;

    public function __construct(private readonly mixed $data) {}

    public function isCreationSchemaValid(): bool
    {
        // validation schema
        $schemaValidation = v::attribute('name', v::stringType()->length(
            self::MINIMUM_NAME_LENGTH, self::MAXIMUM_NAME_LENGTH));
        if (empty($this->data->name))
        {
            throw new ValidationException('Name is empty.');
        }
        if (!$schemaValidation->validate($this->data))
        {
            throw new ValidationException(sprintf('Name is out of range (%d, %d).',
                self::MINIMUM_NAME_LENGTH, self::MAXIMUM_NAME_LENGTH));
        }

        $schemaValidation = $schemaValidation->attribute('email', v::email(), mandatory: false);
        if (!$schemaValidation->validate($this->data))
        {
            throw new ValidationException('Invalid Email.');
        }

        $schemaValidation = $schemaValidation->attribute('phone', v::phone(), mandatory: false);
        if (!$schemaValidation->validate($this->data))
        {
            throw new ValidationException('Invalid Phone.');
        }

        return true;
    }

    public function isUpdateSchemaValid(): bool
    {
        // same schema for both creation and update
        return $this->isCreationSchemaValid();
    }
}