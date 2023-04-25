<?php

!defined('ROOT') && define('ROOT', dirname(__DIR__, 1));

require ROOT . '/vendor/autoload.php';

use Gonzo\Sources\Application;

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

$app = new Application();
$app->execute();