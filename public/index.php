<?php

!defined('ROOT') && define('ROOT', dirname(__DIR__, 1));

require ROOT . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

if ($_ENV['MODE'] === 'prod') {
    error_reporting(0);
} else {
    error_reporting(E_ALL ^ E_DEPRECATED);
}

use Gonzo\Sources\Application;

$app = new Application();
$app->execute();