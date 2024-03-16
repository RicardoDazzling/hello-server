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

class UserService
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(mixed $data): array|User
    {
        $userValidation = new UserValidation($data);
        if ($userValidation->isCreationSchemaValid()) {
            $userUuid = Uuid::uuid4(); // assigning a UUID to the user

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

            try {
                UserDal::create($user);
            } catch (SQL $exception) {
                // Set an internal error when we cannot add an entry to the database
                Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

                // Set to empty result, because an issue happened. The client has to handle this properly
                $data = [];
            }

            return $data;
        }

        // line never accessed, if schema is invalid a "ValidationException" is created.
        throw new ValidationException("Invalid user payload");
    }

    public function retrieve_all(): array
    {
        $users = UserDal::getAll();

        return array_map(function (object $user): object {
            // Remove unnecessary "id" field
            unset($user['id']);
            return $user;
        }, $users);
    }



    public function retrieve(string $uuid): array
    {

        if (v::uuid()->validate($uuid)) {
            if ($user = UserDal::get($uuid)) {
                // Removing fields we don't want to expose
                unset($user['id']);

                return $user;
            }
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
            return [];
        }

        throw new ValidationException("Invalid user UUID");
    }

    public function update(mixed $postBody): array|User
    {
        // validation schema
        $userValidation = new UserValidation($postBody);
        if ($userValidation->isUpdateSchemaValid()) {
            $uuid = $postBody->uuid;

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

            $result = UserDal::update($uuid, $user);
            if ($result) {
                return $postBody;
            }

            return [];
        }

        throw new ValidationException("Invalid user payload");
    }

    public function remove(mixed $data): bool
    {
        $userValidation = new UserValidation($data);
        if ($userValidation->isRemoveSchemaValid()) {
            return UserDal::remove($data->userUuid);
        }
        throw new ValidationException("Invalid user UUID");
    }
}