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
    public function create(array $data): User
    {
        UserValidation::isCreationSchemaValid($data);

        $user = new User();
        $user
            ->setUuid(Uuid::uuid4()->toString())
            ->setStatus($data['status'])
            ->setName($data['first'])
            ->setEmail($data['email'])
            ->setDefault($data['default'])
            ->setCreationDate(date(self::DATE_TIME_FORMAT));

        $user = UserDAL::create($user);
        if ($user->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
        return $user;
    }

    public function retrieve_all(): array
    {
        $users = UserDAL::getAll();
        if(count($users) <= 0)
        {
            return [];
        }
        return array_map(function (User $user): array {
            return $user->getData();
        }, $users);
    }



    public function retrieve(?string $uuid = null, ?string $email = null): User
    {
        if(!is_null($uuid))
        {
            if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid user UUID");

            $user = UserDAL::get($uuid);

            if ($user->isEmpty()) {
                Http::setHeadersByCode(StatusCode::NOT_FOUND);
            }
            return $user;
        }
        if(!is_null($email))
        {
            if (!v::email()->validate($email)) throw new ValidationException("Invalid user EMail");

            $user = UserDAL::get_by_email($email);

            if ($user->isEmpty()) {
                Http::setHeadersByCode(StatusCode::NOT_FOUND);
            }
            return $user;
        }
        throw new ValidationException('Missing data: uuid and email.');
    }

    public function update(array $postBody, string $uuid): User
    {
        if (!(v::uuid()->validate($uuid)))
        {
            throw new ValidationException("Invalid user UUID");
        }
        UserValidation::isUpdateSchemaValid($postBody);

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
        catch (SQL)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return new User();
        }
    }
}