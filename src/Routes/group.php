<?php

use DazzRick\HelloServer\Enums\GroupAction;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;

$userAction = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => GroupAction::POST, // send 201
    'GET' => GroupAction::GET, // send 200
    'DELETE' => GroupAction::DELETE, // send 204 status code
    default => throw new MethodNotAllowedException(), // send 405
};


echo $userAction->getResponse();