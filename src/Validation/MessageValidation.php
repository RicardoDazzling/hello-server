<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\Entity\File;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait MessageValidation
{
    use BaseValidation { BaseValidation::isCreationSchemaValid as isBaseCreationSchemaValid;}
    public static function isCreationSchemaValid(array $data): array
    {
        $new_data = self::isBaseCreationSchemaValid($data);
        if(!array_key_exists('content', $new_data))
            throw new ValidationException('Missing "content" inside the payload: '. "\n". json_encode($new_data));
        if(!v::stringType()->length(1)->validate(trim($new_data['content'])))
            throw new ValidationException('Content is empty');
        $_REQUEST['filter'] = 'sent';
        return $new_data;
    }

    public static function isUpdateSchemaValid(array $data, string $classification, mixed $old_entity): array
    {
        $_REQUEST['filter'] = 'sent';
        if(array_key_exists('content', $data))
        {
            if($classification !== self::SENDER)
                throw new BadRequestException("The message content can only be edited by sender.");
            if(!v::stringType()->length(1)->validate(trim($data['content'])))
                throw new ValidationException('Content is empty.');
        }
        if(array_key_exists('sent', $data))
            throw new ValidationException('Sent time can\'t be setted.');
        if(array_key_exists('received', $data)) {
            throw new ValidationException('Received time can\'t be setted.');
        }
        if(array_key_exists('read', $data)) {
            if($classification !== self::RECEIVER)
                throw new BadRequestException("The message state can only be edited by receiver.");
            if(!is_null($old_entity->getRead()))
                throw new ValidationException("Message already read before.");
            $data['read'] = intdiv(time(), 60);
            $_REQUEST['filter'] = 'read';
        }
        return $data;
    }
}