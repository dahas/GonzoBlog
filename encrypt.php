<?php

if(!isset($argv[1])) {
    die("Error: No E-Mail address provided!" . PHP_EOL);
}

if(!filter_var($argv[1], FILTER_VALIDATE_EMAIL)) {
    die("Error: Not a valid E-Mail address!" . PHP_EOL);
}

echo password_hash($argv[1], PASSWORD_DEFAULT) . PHP_EOL;
