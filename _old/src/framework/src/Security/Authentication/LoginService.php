<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Tulia\Framework\Security\Authentication\Exception\LoginException;
use Tulia\Framework\Security\Authentication\LoginCredentials\LoginCredentialsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LoginService implements LoginServiceInterface
{
    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param AuthenticationManagerInterface $authenticationManager
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    )
    {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage          = $tokenStorage;
        $this->requestStack          = $requestStack;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Implements multiple login providers to make possible login with
     * Facebook, Google, LDAP, oAuth etc. Add Authentication/Login listeners
     * or somethings like that.
     */
    public function login(LoginCredentialsInterface $credentials): void
    {
        $token = $this->createAuthenticationToken($credentials);

        try {
            $authenticatedToken = $this->authenticationManager->authenticate($token);

            $this->tokenStorage->setToken($authenticatedToken);
        } catch (AuthenticationException $e) {
            $request = $this->requestStack->getMasterRequest();

            if ($request && $request->hasSession()) {
                $request->getSession()->set(Security::LAST_USERNAME, $token->getUsername());
                $request->getSession()->set(Security::AUTHENTICATION_ERROR, [
                    'messageKey'  => 'givenLoginCredentialsAreInvalid',
                    'messageData' => [],
                ]);
            }

            /**
             * Sleep prevents "cheeap" Brute Force attacks. Every wrong credentials
             * combination make response 5 seconds longer, making Brute Force
             * attacks less profitable.
             */
            sleep(5);

            throw new LoginException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param LoginCredentialsInterface $credentials
     *
     * @return TokenInterface
     */
    private function createAuthenticationToken(LoginCredentialsInterface $credentials): TokenInterface
    {
        return new UsernamePasswordToken($credentials->get('username'), $credentials->get('password'), 'tulia');
    }
}
