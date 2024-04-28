<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\ParticipantDAL;
use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Entity\Participant;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use DazzRick\HelloServer\Exceptions\ValidationException;
use DazzRick\HelloServer\Validation\ParticipantValidation;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use RedBeanPHP\RedException\SQL;
use Respect\Validation\Validator as v;

class ParticipantService implements Serviceable
{
    use ParticipantValidation;

    private ?string $email;

    private ?string $group_uuid;

    public function __construct(?string $email = null, ?string $group_uuid = null) {
        $this->group_uuid = $group_uuid;
        $this->email = $email;
    }

    public function create(array $data): Participant
    {
        $data = self::isCreationSchemaValid($data);

        $participant = new Participant();
        $participant->setData($data);

        $participant = ParticipantDAL::create($participant);
        if ($participant->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        else $participant->setUser(empty($data['user'])?$GLOBALS['jwt']->getEmail():$data['user']);
        return $participant;
    }

    public function retrieve_all(): array
    {
        $participants = empty($this->group_uuid)
            ?ParticipantDAL::getAll(user: $GLOBALS['jwt']->getUuid())
            :ParticipantDAL::getAll(group: $this->group_uuid);
        if (count($participants) <= 0) return [];
        $uuid_list = array_map(function (Participant $participant) { return $participant->getUser(); }, $participants);
        $users_emails = UserDAL::get_email_from_uuid($uuid_list);
        $new_array = [];
        $length = count($uuid_list);
        $participants = array_values($participants);
        for ($i = 0; $i < $length; $i++)
            $new_array[] = $participants[$i]->setUser($users_emails[$participants[$i]->getUser()])->getData();;
        return $new_array;
    }

    public function retrieve(string $uuid): Participant
    {
        if (!v::uuid()->validate($uuid)) throw new ValidationException("Invalid group UUID");

        $user = empty($this->email) ? $GLOBALS['jwt']->getUuid() : UserDAL::get_by_email($this->email)->getUuid();
        $participant = ParticipantDAL::get(user: $user, group: $uuid);

        if ($participant->isEmpty()) Http::setHeadersByCode(StatusCode::NOT_FOUND);
        else $participant->setUser(empty($this->email) ? $GLOBALS['jwt']->getEmail() : $this->email);
        return $participant;
    }

    /**
     * @param array $postBody
     * @param string|null $uuid
     * @return Participant
     * @throws SQL
     */
    public function update(array $postBody, ?string $uuid = null): Participant
    {
        # if(is_null($this->group_uuid) && array_key_exists('group', $postBody))
        #     throw new BadRequestException('The group UUID need to be set by query argument, and not PostBody!');
        if(!empty($this->email)){
            $user = UserDAL::get_by_email($this->email);
            if($user->isEmpty())
                throw new BadRequestException("User with '$this->email' email doesn't exist");
            $user_id = $this->email;
            $postBody['user'] = $user->getUuid();
        }
        else {
            $user_id = $GLOBALS['jwt']->getUuid();
            $postBody['user'] = $user_id;
        }
        $postBody['group'] = $this->group_uuid;
        $old_entity = self::isUpdateSchemaValid($postBody);

        $participant = (new Participant())->setData($postBody);
        $participant = ParticipantDAL::update($participant);

        if ($participant->isEmpty()) Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        else $participant = $old_entity->setData($postBody)->setUser($user_id);

        return $participant;
    }

    public function remove(?string $uuid): Participant
    {
        if (is_null($uuid)) throw new BadRequestException('Group uuid is null.');
        if (!v::uuid()->validate($uuid)) throw new BadRequestException('Invalid uuid');
        global $jwt;
        $sender = ParticipantDAL::get($jwt->getUuid(), $uuid);
        if(!$sender->isAdmin())
            throw new UnAuthorizedException('Only administrators can remove a participant');

        if(!empty($this->email)){
            $user = UserDAL::get_by_email($this->email);
            if($user->isEmpty())
                throw new BadRequestException("User with '$this->email' email doesn't exist");
            $user_id = $this->email;
            $user_uuid = $user->getUuid();
            if(ParticipantDAL::get($user_uuid, $uuid)->isSuper())
                throw new UnAuthorizedException("Super User can't be removed!");
        }
        else $user_id = $user_uuid = $jwt->getUuid();

        try {
            $entity = ParticipantDAL::remove(user:$user_uuid, group: $uuid);
            if ($entity->isEmpty())
                throw new NotFoundException("Unknown participant.");

            $entity->setUser($user_id);
            if($entity->isSuper())
                ParticipantDAL::update_super($uuid);
            return $entity;
        }
        catch (SQL)
        {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
            return new Participant();
        }
    }
}