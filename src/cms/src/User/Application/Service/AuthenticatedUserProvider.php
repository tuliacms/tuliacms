<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class AuthenticatedUserProvider implements AuthenticatedUserProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(TokenStorageInterface $tokenStorage, FinderFactoryInterface $finderFactory)
    {
        $this->tokenStorage  = $tokenStorage;
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): User
    {
        $token = $this->tokenStorage->getToken();

        if (! $token) {
            $user = null;
        } else {
            if ($this->user) {
                return $this->user;
            }

            $user = $this->finderFactory->getInstance(ScopeEnum::INTERNAL)->findByUsername($token->getUsername());
        }

        if (! $user) {
            $user = $this->getDefaultUser();
        }

        return $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUser(): User
    {
        return new User();
    }
}
