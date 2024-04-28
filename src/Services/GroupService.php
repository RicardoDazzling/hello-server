<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\GroupDAL;
use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\Entity\Group;
use DazzRick\HelloServer\Entity\Participant;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\GroupValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class GroupService implements Serviceable
{
    use GroupValidation;

    public function create(array $data): Group
    {
        self::isCreationSchemaValid($data);

        $group = new Group();
        $group
            ->setUuid(Uuid::uuid4()->toString())
            ->setPhoto($data['photo'] ?? null)
            ->setName($data['name'] ?? null)
            ->setDescription($data['description'] ?? null)
            ->setCreation(intdiv(time(), 60));

        $group = GroupDAL::create($group);
        if ($group->isEmpty()) throw new InternalServerException("Group creation failed");
        else {
            $super_participant = new Participant();
            $super_participant
                ->setUser($GLOBALS['jwt']->getUuid())
                ->setGroup($group->getUuid())
                ->isActive(true)
                ->isAdmin(true)
                ->isSuper(true);

            $super_participant = ParticipantDAL::create($super_participant);

            if ($super_participant->isEmpty()) {
                GroupDAL::remove($group->getUuid());
                throw new InternalServerException("Super User creation failed");
            }
        }
        return $group;
    }

    public function retrieve_all(): array
    {
        $groups = GroupDAL::getSelection(ParticipantDAL::retrieve_all_groups_from_user());
        if(count($groups) <= 0) return [];
        return array_map(function (Group $group): array {
            return $group->getData();
        }, array_values($groups));
    }

    public function retrieve(string $uuid): Group
    {
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid user UUID");

        $user = GroupDAL::get($uuid);

        if ($user->isEmpty()) Http::setHeadersByCode(StatusCode::NOT_FOUND);
        return $user;
    }

    public function update(array $postBody, string $uuid): Group
    {
        if (!(v::uuid()->validate($uuid))) throw new ValidationException("Invalid user UUID");
        self::isUpdateSchemaValid($postBody);

        $group = (new Group())->setData($postBody)->setUuid($uuid);
        self::userVerification($uuid, true);
        $group = GroupDAL::update($group);

        if ($group->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        return $group;
    }

    public function remove(?string $uuid): Group
    {
        if (is_null($uuid)) throw new ValidationException("UUID is required.");
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid user UUID");
        self::userVerification($uuid, true, true);
        $entity = GroupDAL::remove($uuid);
        if ($entity->isEmpty()) throw new ValidationException("Unknown user.");
        else {
            try {
                ParticipantDAL::remove_all_from_group($uuid);
            } catch (\Exception $e) {
                GroupDAL::create($entity);
                throw new InternalServerException("Remove participants failed.");
            }
        }
        return $entity;
    }
}