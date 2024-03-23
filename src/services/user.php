<?php
namespace DazzRick\HelloServer\Services;
use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\UserValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class UserService
{
    public const string DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(mixed $data): array|User
    {
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {
            $userUuid = Uuid::uuid4()->toString();

            $user = new User();
            $user
                ->setUuid($userUuid)
                ->setStatus($data->status)
                ->setTarget($data->target)
                ->setName($data->first)
                ->setEmail($data->email)
                ->setPhone($data->phone)
                ->setDefault($data->default)
                ->setCreationDate(date(self::DATE_TIME_FORMAT));

            if (UserDAL::create($user) === false) {
                Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

                return [];
            }

            return $user;
        }

        // line never accessed, if schema is invalid a "ValidationException" is created.
        throw new ValidationException("Invalid user payload");
    }

    public function retrieve_all(): array
    {
        $users = UserDAL::getAll();

        return array_map(function (object $user): object {
            // Remove unnecessary "id" field
            unset($user['id']);
            return $user;
        }, $users);
    }



    public function retrieve(string $uuid): array
    {

        if (v::uuid()->validate($uuid)) {
            if ($user = UserDAL::get($uuid)) {
                // Removing fields we don't want to expose
                unset($user['id']);

                return $user;
            }
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
            return [];
        }

        throw new ValidationException("Invalid user UUID");
    }

    public function update(mixed $postBody, string $uuid): array|User
    {
        if (!(v::uuid()->validate($uuid)))
        {
            throw new ValidationException("Invalid user UUID");
        }

        // validation schema
        $userValidation = new UserValidation($postBody);
        if ($userValidation->isUpdateSchemaValid())
        {
            $user = new User();
            if (!empty($postBody->name)) {
                $user->setName($postBody->name);
            }

            if (!empty($postBody->status)) {
                $user->setStatus($postBody->status);
            }

            if (!empty($postBody->target)) {
                $user->setTarget($postBody->target);
            }

            if (!empty($postBody->default)) {
                $user->setDefault($postBody->default);
            }

            if (UserDAL::update($uuid, $user) === false) {
                Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
                return [];
            }

            return $postBody;
        }

        throw new ValidationException("Invalid user payload");
    }

    public function remove(?string $uuid): array|true
    {
        if(is_null($uuid)){
            throw new ValidationException("UUID is required.");
        }
        if (v::uuid()->validate($uuid)) {
            $result = UserDAL::remove($uuid);
            if ($result === null){
                throw new ValidationException("Unknown user.");
            }
            if ($result === false){
                Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
                return [];
            }
            return true;
        }
        throw new ValidationException("Invalid user UUID");
    }
}