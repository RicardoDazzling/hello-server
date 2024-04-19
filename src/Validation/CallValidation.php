<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait CallValidation
{
    use BaseValidation { BaseValidation::isCreationSchemaValid as isBaseCreationSchemaValid;}

    public static function isCreationSchemaValid(array $data): array
    {
        $new_data = self::isBaseCreationSchemaValid($data);
        if(!array_key_exists('audio', $new_data))
            throw new ValidationException('Missing "audio" inside the payload: '. "\n". json_encode($data));
        if(!v::base64()->validate($new_data['audio']))
            throw new ValidationException('Invalid audio.');
        if(array_key_exists('video', $new_data))
            if(!v::base64()->validate($new_data['video']))
                throw new ValidationException('Invalid video.');
        return $new_data;
    }

    public static function isUpdateSchemaValid(array $data, string $classification, mixed $old_entity): array
    {
        if(array_key_exists('audio', $data))
            if($classification !== self::SENDER)
                throw new BadRequestException('The call audio can only be edited by sender.');
            if(!v::base64()->validate($data['audio']))
                throw new ValidationException('Invalid audio.');
        if(array_key_exists('video', $data))
            if($classification !== self::SENDER)
                throw new BadRequestException('The call video can only be edited by sender.');
            if(!v::base64()->validate($data['video']))
                throw new ValidationException('Invalid video.');
        return $data;
    }
}