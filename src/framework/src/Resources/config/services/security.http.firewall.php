<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Security\Http\Firewall\AnonymousAuthenticationListener;
use Tulia\Framework\Security\Http\Firewall\BackendAccess;
use Tulia\Framework\Security\Http\Firewall\ContextListener;

$builder->setDefinition(ContextListener::class, ContextListener::class, [
    'arguments' => [
        tagged('security.user_provider'),
        service(TokenStorageInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 1000, 'onRequest'),
        tag_event_listener(ResponseEvent::class, 1000, 'onResponse'),
    ],
]);

$builder->setDefinition(AnonymousAuthenticationListener::class, AnonymousAuthenticationListener::class, [
    'arguments' => [
        service(TokenStorageInterface::class),
        service(AuthenticationManagerInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 500, 'onRequest'),
    ],
]);

$builder->setDefinition(BackendAccess::class, BackendAccess::class, [
    'arguments' => [
        service(TokenStorageInterface::class),
        service(AccessDecisionManagerInterface::class),
        service(RouterInterface::class),
        parameter('security.authentication.login_path'),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 100, 'onRequest'),
    ],
]);

/*$builder->setDefinition(Firewall::class, Firewall::class, [
    'arguments' => [
        service(FirewallMapInterface::class),
        service(EventDispatcherInterface::class),
    ],
]);

$builder->setDefinition(FirewallMapInterface::class, FirewallMap::class);*/
