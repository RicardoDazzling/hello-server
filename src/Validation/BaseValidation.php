<?php

namespace DazzRick\HelloServer\Validation;

use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait BaseValidation
{
    public static function isCreationSchemaValid(array $data): array
    {
        if(!array_key_exists('to', $data))
            throw new ValidationException('Missing "to" inside the payload.: '. "\n". json_encode($data));

        if(!v::email()->validate($data['to']))
            throw new ValidationException('Invalid receiver email');
        else $data['to_email'] = $data['to'];

        $user = UserDAL::get_by_email($data['to']);
        if($user->isEmpty())
            throw new ValidationException("Receiver doesn't exist.");
        else $data['to_uuid'] = $user->getUuid();
        unset($data['to']);

        return $data;
    }

    public static function isUpdateSchemaValid(array $data, string $classification, mixed $old_entity): array
    {
        throw new BadRequestException('This resource can\'t be updated.');
    }
}