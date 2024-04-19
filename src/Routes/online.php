<?php

use DazzRick\HelloServer\DAL\UserDAL;
use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;
use Respect\Validation\Validator as v;

function getResponse(): string
{
    $content = file_get_contents('php://input');
    $postBody = (array) json_decode($content);
    array_map(function (string $email) {
        if(!(v::email()->validate($email))) throw new BadRequestException("Search Email is invalid: '".$email."'.");
    }, $postBody);
    $online_list = (new UserDAL())->online($postBody);
    return json_encode($online_list);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new MethodNotAllowedException();

echo getResponse();
