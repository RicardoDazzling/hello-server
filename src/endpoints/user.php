<?php
namespace DazzRick\HelloServer;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Models\User;
use DazzRick\HelloServer\Validation\UserValidation;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class UserEndPoint
{
    public function create(mixed $data): User
    {
        // validate data
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {
            return new User(
                Uuid::uuid4(),
                $data->name,
                $data->phone ?? null,
                $data->email ?? null
            ); // return statement exists the function and doesn't go beyond this scope
        }

        // line never accessed, if schema is invalid a "ValidationException" is created.
        throw new ValidationException("Invalid user payload");
    }

    public function retrieve_all(): array
    {
        // TODO: bind database with retrieve and do it for all uuids;
        return [];
    }

    public function retrieve(string $uuid): User
    {
        if (v::uuid()->validate($uuid)) {
            // TODO: bind database with these method to set name, phone and email, if exists;
            return new User($uuid, 'name', 'phone', 'email');
        }

        throw new ValidationException("Invalid user UUID");
    }

    public function update(mixed $postBody): object
    {
        // validation schema
        $userValidation = new UserValidation($postBody);
        if ($userValidation->isUpdateSchemaValid()) {
            return $postBody;
        }

        throw new ValidationException("Invalid user payload");
    }

    public function remove(string $uuid): bool
    {
        if (v::uuid()->validate($uuid)) {
            // TODO Lookup the the DB user row with this userId
            return true; // default value
        }

        throw new ValidationException("Invalid user UUID");
    }
}