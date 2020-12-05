<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Security\Authentication\LoginService;
use Tulia\Framework\Security\Authentication\LoginServiceInterface;
use Tulia\Framework\Security\Authentication\LogoutService;
use Tulia\Framework\Security\Authentication\LogoutServiceInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(AuthenticationManagerInterface::class, AuthenticationProviderManager::class, [
    'factory' => function ($providers, $debug, $eventDispatcher) {
        $manager = new AuthenticationProviderManager($providers, $debug);
        $manager->setEventDispatcher($eventDispatcher);

        return $manager;
    },
    'arguments' => [
        tagged('security.authentication_provider'),
        ! $builder->getParameter('kernel.debug'),
        service(EventDispatcherInterface::class),
    ],
]);

$builder->setDefinition(DaoAuthenticationProvider::class, DaoAuthenticationProvider::class, [
    'arguments' => [
        service(UserProviderInterface::class),
        service(UserCheckerInterface::class),
        'tulia',
        service(EncoderFactoryInterface::class),
        ! $builder->getParameter('kernel.debug'),
    ],
    'tags' => [ tag('security.authentication_provider') ],
]);

$builder->setDefinition(AnonymousAuthenticationProvider::class, AnonymousAuthenticationProvider::class, [
    'arguments' => [
        'tulia',
    ],
    'tags' => [ tag('security.authentication_provider') ],
]);

$builder->setDefinition(TokenStorageInterface::class, TokenStorage::class);

$builder->setDefinition(AuthenticationTrustResolverInterface::class, AuthenticationTrustResolver::class);

$builder->setDefinition(LoginServiceInterface::class, LoginService::class, [
    'arguments' => [
        service(AuthenticationManagerInterface::class),
        service(TokenStorageInterface::class),
        service(RequestStack::class),
    ],
]);

$builder->setDefinition(LogoutServiceInterface::class, LogoutService::class, [
    'arguments' => [
        service(TokenStorageInterface::class),
        service(RequestStack::class),
    ],
]);
