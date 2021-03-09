<?php

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\Dotenv\Dotenv;

include __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

if (file_exists(__DIR__ . '/.env')) {
    $_ENV['APP_STATUS'] = 'WORKING';
} else {
    $_ENV['APP_STATUS'] = 'INSTALLATION';
}

if ($_SERVER['APP_ENV'] === 'dev') {
    error_reporting(-1);
} else {
    error_reporting(0);
}

if ($_SERVER['APP_DEBUG']) {
    DebugClassLoader::enable();
    Debug::enable();
    ErrorHandler::register();
} else {
    ErrorHandler::register();
}

function tulia_installed(): bool
{
    return $_ENV['APP_STATUS'] !== 'INSTALLATION';
}
