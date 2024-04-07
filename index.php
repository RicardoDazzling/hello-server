<?php

namespace DazzRick\HelloServer;

use Whoops\Run as WhoopsRun;
use Whoops\Handler\JsonResponseHandler as WhoopsJsonResponseHandler;

require __DIR__ . '/vendor/autoload.php';

// handle all exceptions and convert them into JSON format
$whoops = new WhoopsRun();
$whoops->pushHandler(new WhoopsJsonResponseHandler);
$whoops->register();


require __DIR__ . '/src/Config/config.inc.php';
require __DIR__ . '/src/Config/database.inc.php'; // TODO Could find sth cleaner
require __DIR__ . '/src/Helpers/headers.inc.php';
require __DIR__ . '/src/Routes/routes.php';