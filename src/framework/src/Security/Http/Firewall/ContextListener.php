<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Firewall;

use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;
use Tulia\Framework\Kernel\Event\ResponseEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ContextListener
{
    private const SESSION_KEY = '_security_user_token';

    /**
     * @var iterable
     */
    protected $userProviders;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param iterable              $userProviders
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(iterable $userProviders, TokenStorageInterface $tokenStorage)
    {
        $this->userProviders = $userProviders;
        $this->tokenStorage  = $tokenStorage;
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->hasPreviousSession() && $request->hasSession() ? $request->getSession() : null;
        $token   = null;

        if (null !== $session) {
            $token = $session->get(self::SESSION_KEY);
        }

        if (null === $session || null === $token) {
            $this->tokenStorage->setToken(null);

            return;
        }

        if ($token instanceof TokenInterface) {
            $token = $this->refreshUser($token);
        } elseif (null !== $token) {
            $token = null;
        }

        $this->tokenStorage->setToken($token);
    }

    /**
     * @param ResponseEvent $event
     */
    public function onResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $token   = $this->tokenStorage->getToken();

        if (null === $token || $token instanceof AnonymousToken) {
            if ($request->hasPreviousSession()) {
                $session->remove(self::SESSION_KEY);
            }
        } else {
            $session->set(self::SESSION_KEY, $token);
        }
    }

    /**
     * @param TokenInterface $token
     *
     * @return TokenInterface
     */
    private function refreshUser(TokenInterface $token): TokenInterface
    {
        if (! tulia_installed()) {
            return new AnonymousToken('tulia', 'anon.', []);
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return $token;
        }

        $userDeauthenticated = false;
        $userNotFoundByProvider = false;
        $userClass = \get_class($user);

        foreach ($this->userProviders as $provider) {
            if (!$provider instanceof UserProviderInterface) {
                throw new \InvalidArgumentException(sprintf('User provider "%s" must implement "%s".', \get_class($provider), UserProviderInterface::class));
            }

            if (!$provider->supportsClass($userClass)) {
                continue;
            }

            try {
                $refreshedUser = $provider->refreshUser($user);
                $newToken = clone $token;
                $newToken->setUser($refreshedUser);

                // Tokens can be deauthenticated if the user has been changed.
                if (!$newToken->isAuthenticated()) {
                    $userDeauthenticated = true;

                    continue;
                }

                $token->setUser($refreshedUser);

                return $token;
            } catch (UnsupportedUserException $e) {
                // let's try the next user provider
            } catch (UsernameNotFoundException $e) {
                $userNotFoundByProvider = true;
            }
        }

        if ($userDeauthenticated) {
            return new AnonymousToken('tulia', 'anon.', []);
        }

        /*if ($userNotFoundByProvider) {
            return null;
        }*/

        throw new \RuntimeException(sprintf('There is no user provider for user "%s". Shouldn\'t the "supportsClass()" method of your user provider return true for this classname?', $userClass));
    }
}
