<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Http\Session\SessionRegistrator;
use Tulia\Framework\Kernel\Event\RequestEvent;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(SessionHandlerInterface::class, NativeFileSessionHandler::class);
$builder->setDefinition(SessionStorageInterface::class, NativeSessionStorage::class, [
    'arguments' => [
        [
            'cookie_httponly' => true,
            'cookie_lifetime' => 60 * 60,
            'cookie_path'     => '/',
            'cookie_secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'name'            => 'tuliasid',
        ],
        service(SessionHandlerInterface::class),
    ],
]);
$builder->setDefinition(SessionInterface::class, Session::class, [
    'arguments' => [
        service(SessionStorageInterface::class),
    ],
]);

$builder->setDefinition(SessionRegistrator::class, SessionRegistrator::class, [
    'arguments' => [
        service(SessionInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 2000, 'register'),
    ],
]);
