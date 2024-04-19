<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\ValidationException;

trait LostValidation
{
    use BaseValidation { BaseValidation::isCreationSchemaValid as isBaseCreationSchemaValid;}
    public static function isCreationSchemaValid(array $data): array
    {
        $new_data = self::isBaseCreationSchemaValid($data);
        if(!array_key_exists('type', $new_data))
            throw new ValidationException('Missing "type" inside the payload: ' . "\n". json_encode($data));
        if(in_array($new_data['type'], ['audio', 'video'], TRUE))
            throw new ValidationException('Invalid type.');
        return $new_data;
    }
}