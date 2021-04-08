<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LogoutService implements LogoutServiceInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function logout(): void
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request && $request->hasSession()) {
            $request->getSession()->invalidate();
        }

        $this->tokenStorage->setToken(null);
    }
}
