<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait FileValidation
{
    use BaseValidation { BaseValidation::isCreationSchemaValid as isBaseCreationSchemaValid;}
    use MessageValidation { MessageValidation::isUpdateSchemaValid as messageIsUpdateSchemaValid; }

    public static function isCreationSchemaValid(array $data): array
    {
        $new_data = self::isBaseCreationSchemaValid($data);
        if(!array_key_exists('content', $new_data))
            throw new ValidationException('Missing "content" inside the payload: '. "\n". json_encode($data));
        if(!v::base64()->length(1)->validate(explode(',', trim($new_data['content']))[1]))
            throw new ValidationException('Content is empty or is not a base64');
        return $new_data;
    }

    public static function isUpdateSchemaValid(array $data, string $classification, mixed $old_entity): array
    {
        $data = self::messageIsUpdateSchemaValid($data, $classification, $old_entity);
        if(array_key_exists('opened', $data)) {
            if($classification !== self::RECEIVER)
                throw new BadRequestException("The file state can only be edited by receiver.");
            if(!is_null($old_entity->getOpen()))
                throw new ValidationException("Message already opened before.");
            $data['opened'] = intdiv(time(), 60);
            $_REQUEST['filter'] = 'opened';
        }
        return $data;
    }
}