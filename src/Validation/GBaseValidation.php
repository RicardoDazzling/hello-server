<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\GFileDAL;
use DazzRick\HelloServer\DAL\GMessageDAL;
use DazzRick\HelloServer\DAL\GroupDAL;
use DazzRick\HelloServer\Entity\GFile;
use DazzRick\HelloServer\Entity\GMessage;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait GBaseValidation
{
    public static function isCreationSchemaValid(array $data): array
    {
        if(!array_key_exists('to', $data))
            throw new ValidationException('Missing "to" inside the payload.: '. "\n". json_encode($data));

        if(!v::uuid()->validate($data['to']))
            throw new ValidationException('Invalid group uuid.');

        $group = GroupDAL::get($data['to']);
        if($group->isEmpty())
            throw new ValidationException("Group doesn't exist.");
        else $data['to_uuid'] = $group->getUuid();
        unset($data['to']);

        if(!array_key_exists('content', $data))
            throw new ValidationException('Missing "content" inside the payload: '. "\n". json_encode($data));
        if(static::TYPE === GFileDAL::TABLE_NAME)
            if(!v::base64()->length(1)->validate(trim($data['content'])))
                throw new ValidationException('Content is empty or is not a base64');
        if(static::TYPE === GMessageDAL::TABLE_NAME)
            if(!v::stringType()->length(1)->validate(trim($data['content'])))
                throw new ValidationException('Content is empty.');

        return $data;
    }

    public static function isUpdateSchemaValid(array $data, string $classification, GMessage|GFile $old_entity): array
    {
        if(!array_key_exists('content', $data))
            throw new ValidationException('Missing "content" inside the payload: '. "\n". json_encode($data));
        if($classification !== static::SENDER)
            throw new ValidationException('Only the sender can update messages');
        if(static::TYPE === GFileDAL::TABLE_NAME)
            if(!v::base64()->length(1)->validate(trim($data['content'])))
                throw new ValidationException('Content is empty or is not a base64');
        if(static::TYPE === GMessageDAL::TABLE_NAME)
            if(!v::stringType()->length(1)->validate(trim($data['content'])))
                throw new ValidationException('Content is empty.');
    }
}