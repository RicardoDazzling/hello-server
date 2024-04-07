<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\VerificationDAL;
use DazzRick\HelloServer\Entity\User;
use DazzRick\HelloServer\Entity\Verification;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\InternalServerException;
use DazzRick\HelloServer\Exceptions\UnAuthorizedException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Respect\Validation\Validator as v;

class VerificationService
{
    public function create(User $user_entity): void
    {
        $code = MailerService::sendVerification($user_entity);
        $entity = new Verification();
        $entity -> setUuid($user_entity->getUuid())
            ->setCode($code)
            ->setTryNumber(0);
        $entity = VerificationDAL::create($entity);
        if ($entity->isEmpty()) {
            Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function verify(string $code, ?User $entity = null, ?string $uuid = null): void
    {
        if(!is_null($entity)) $uuid = $entity->getUuid();
        if(is_null($uuid)) throw new BadRequestException('User Entity or User UUID is required.');
        if(!v::uuid()->validate($uuid)) throw new UnAuthorizedException('Invalid UUID.');
        $entity = VerificationDAL::get($uuid);
        if($entity->isEmpty()) throw new BadRequestException('UUID not found.');
        $try_number = $entity->getTryNumber() - 2 + 1; // +1 counting this time;
        if($try_number >= 1)
        {
            $wait_time = $entity->getLastTry() + (5 * pow($entity->getTryNumber(), 2));
            if(time() < $wait_time) throw new BadRequestException(
                sprintf('You need to wait until "%s" to continue', date(Serviceable::DATE_TIME_FORMAT, $wait_time)));
        }
        if($code !== $entity->getCode())
        {
            $entity->setTryNumber($entity->getTryNumber() + 1);
            $entity->setLastTry(time());
            VerificationDAL::update($entity);
            throw new BadRequestException('Incorrect code.');
        }
        VerificationDAL::remove($uuid);
    }
}