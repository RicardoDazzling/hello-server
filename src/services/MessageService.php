<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\MessageDAL;
use DazzRick\HelloServer\Entity\Message;
use DazzRick\HelloServer\Validation\MessageValidation;

class MessageService extends BaseService implements Serviceable
{
    use MessageValidation;
    public const TYPE = 'message';

    protected static function populateCreateEntity(array $data): Message
    {
        return parent::populateCreateEntity($data)
            ->setSend(intdiv(time(), 60));
    }

    public function create(array $data): Message { return parent::_create($data); }

    public function retrieve_all(string $user_uuid = ''): array { return parent::_retrieve_all($user_uuid); }

    public function retrieve(string $uuid): Message { return parent::_retrieve($uuid); }

    public function update(mixed $postBody, string $uuid): Message { return parent::_update($postBody, $uuid); }

    public function remove(?string $uuid): Message { return parent::_remove($uuid); }

    public function clean(): void { MessageDAL::clean(); }
}