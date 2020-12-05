<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Framework\Security\Http\Csrf\EventListener\ControllerRequestTokenValidator;
use Tulia\Framework\Twig\Extension\CsrfExtension;

$builder->setDefinition(TokenStorageInterface::class, SessionTokenStorage::class, [
    'arguments' => [
        service(SessionInterface::class),
    ],
]);

$builder->setDefinition(CsrfTokenManagerInterface::class, CsrfTokenManager::class, [
    'arguments' => [
        null,
        service(TokenStorageInterface::class),
    ],
]);

$builder->setDefinition(CsrfExtension::class, CsrfExtension::class, [
    'arguments' => [
        service(CsrfTokenManagerInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(ControllerRequestTokenValidator::class, ControllerRequestTokenValidator::class, [
    'arguments' => [
        service(CsrfTokenManagerInterface::class),
        service(LoggerInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class),
    ],
]);
