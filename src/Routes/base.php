<?php
namespace DazzRick\HelloServer\Routes;

use DazzRick\HelloServer\Enums\BaseAction;
use DazzRick\HelloServer\Exceptions\MethodNotAllowedException;

$userAction = match ($_SERVER['REQUEST_METHOD']) {
    'POST' => BaseAction::POST, // send 201
    'GET' => BaseAction::GET, // send 200
    'DELETE' => BaseAction::DELETE, // send 204 status code
    default => throw new MethodNotAllowedException(), // send 405
};

echo $userAction->getResponse();