<?php

!defined('ROOT') && define('ROOT', __DIR__);

require ROOT .'/vendor/autoload.php';

use Gonzo\Sources\Application;

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->safeLoad();

// $hash = password_hash("your_address@gmail.com", PASSWORD_DEFAULT);
// $verified = password_verify('your_address@gmail.com', $hash);

# Set Request Params:
# -----------------------------------------
$_SERVER['REQUEST_URI'] = "/Blog";
$_SERVER['REQUEST_METHOD'] = "GET";

$_POST['title'] = "This is the Title";
$_POST['comment'] = "And here comes a really sweet comment. <script>alert('XSS')</script>";
$_POST['name'] = 'Frank Drebin';
$_POST['email'] = "f.drebin@lapd.org";
# -----------------------------------------


$app = new Application();
$app->execute();
