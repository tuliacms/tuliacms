<?php

use Tulia\Cms\Platform\Infrastructure\Framework\Kernel\TuliaKernel;
use Tulia\Framework\Http\Request;

include __DIR__ . '/../bootstrap.php';

$request = Request::createFromGlobals();

$kernel = new TuliaKernel($_ENV['APP_ENV'], $_ENV['APP_DEBUG']);
$kernel->setProjectDir(dirname(__DIR__));
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
