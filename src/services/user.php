<?php
namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\UserValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;

class UserService implements Serviceable
{
    public function create(mixed $data): User
    {
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {
            $userUuid = Uuid::uuid4()->toString();

            $user = new User();
            $user
                ->setUuid($userUuid)
                ->setStatus($data->status)
                ->setName($data->first)
                ->setEmail($data->email)
                ->setDefault($data->default)
                ->setCreationDate(date(self::DATE_TIME_FORMAT));

            if (($user = UserDAL::create($user))->isEmpty()) {
                Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

                $user = new User();
            }

            return $user;
        }

        // line never accessed, if schema is invalid a "ValidationException" is created.
        throw new ValidationException("Invalid user payload");
    }

    public function retrieve_all(): array
    {
        $users = UserDAL::getAll();
        if(count($users) <= 0)
        {
            return [];
        }
        return array_map(function (User $user): array {
            return $user->data;
        }, $users);
    }



    public function retrieve(string $uuid): User
    {

        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid user UUID");
        }

        $user = UserDAL::get($uuid);

        if ($user->isEmpty()) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        return $user;
    }

    public function update(mixed $postBody, string $uuid): User
    {
        if (!(v::uuid()->validate($uuid)))
        {
            throw new ValidationException("Invalid user UUID");
        }

        // validation schema
        $userValidation = new UserValidation($postBody);

        if (!$userValidation->isUpdateSchemaValid())
        {
            throw new ValidationException("Invalid user payload");
        }

        $user = (new User())->setData($postBody);
        $user->setUuid($uuid);
        $user = UserDAL::update($user);

        if ($user->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $user;
    }

    public function remove(?string $uuid): User
    {
        if(is_null($uuid)){
            throw new ValidationException("UUID is required.");
        }
        if (!v::uuid()->validate($uuid)) {
            throw new ValidationException("Invalid user UUID");
        }
        try {
            $entity = UserDAL::remove($uuid);
            if ($entity->isEmpty()){
                throw new ValidationException("Unknown user.");
            }
            return $entity;
        }
        catch (SQL $e)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return new User();
        }
    }
}