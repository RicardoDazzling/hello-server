<?php
namespace DazzRick\HelloServer\Config;

use RedBeanPHP\R;

// setup RedBean
$dsn = sprintf('mysql:host=%s;dbname=%s;port=%s', $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
R::setup($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);