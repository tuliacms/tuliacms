<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Firewall;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Framework\Kernel\Event\ResponseEvent;

/**
 * @author Adam Banaszkiewicz
 */
class AnonymousAuthenticationListener implements EventSubscriberInterface
{
    protected TokenStorageInterface $tokenStorage;
    protected AuthenticationManagerInterface$authenticationManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager = null,
        ?LoggerInterface $logger = null
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        /*$this->logger = $logger;*/
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', 500],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (null !== $this->tokenStorage->getToken()) {
            return;
        }

        //try {
            $token = new AnonymousToken('tulia', 'anon.', []);
            $token = $this->authenticationManager->authenticate($token);

            $this->tokenStorage->setToken($token);

            /*if (null !== $this->logger) {
                $this->logger->info('Populated the TokenStorage with an anonymous Token.');
            }*/
        //} catch (AuthenticationException $e) {
            /*if (null !== $this->logger) {
                $this->logger->info('Anonymous authentication failed.', ['exception' => $failed]);
            }*/
        //}
    }
}
