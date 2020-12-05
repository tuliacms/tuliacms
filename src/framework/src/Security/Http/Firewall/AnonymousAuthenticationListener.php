<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Firewall;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class AnonymousAuthenticationListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;


    /**
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface|null $authenticationManager
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager = null,
        LoggerInterface $logger = null
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        /*$this->logger = $logger;*/
    }

    /**
     * @param RequestEvent $event
     *
     * @throws RouteNotFoundException
     */
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
