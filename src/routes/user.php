<?php

use DazzRick\HelloServer\Enums\UserAction;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;

$userAction = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => UserAction::POST, // send 201
    'GET' => UserAction::GET, // send 200
    //'DELETE' => UserAction::DELETE, // send 204 status code
    default => throw new MethodNotAllowedException(), // send 405
};


echo $userAction->getResponse();