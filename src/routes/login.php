<?php

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use DazzRick\HelloServer\Exceptions\NotFoundException;
use DazzRick\HelloServer\Services\UserService;
use DazzRick\HelloServer\Services\VerificationService;
use Respect\Validation\Validator as v;

function getResponse(): string
{
    $email = $_REQUEST['email'] ?? null;
    if (is_null($email)) throw new BadRequestException('Email is required.');
    if(!(v::email()->validate($email))) throw new BadRequestException("Email is invalid: '".$email."'.");
    $user = (new UserService())->retrieve(email: $email);
    if($user->isEmpty()) throw new NotFoundException('User not found.');
    (new VerificationService())->create($user);
    return json_encode(['Success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
{
    throw new MethodNotAllowedException();
}

echo getResponse();

