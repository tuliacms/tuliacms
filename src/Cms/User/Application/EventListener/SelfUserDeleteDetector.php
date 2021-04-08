<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Tulia\Cms\User\Application\Event\UserEvent;
use Tulia\Cms\User\Application\Exception\TranslatableUserException;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SelfUserDeleteDetector
{
    /**
     * @var AuthenticatedUserProviderInterface
     */
    protected $authenticatedUserProvider;

    /**
     * @param AuthenticatedUserProviderInterface $authenticatedUserProvider
     */
    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    /**
     * @param UserEvent $event
     *
     * @throws TranslatableUserException
     */
    public function handle(UserEvent $event): void
    {
        $user = $event->getUser();

        if ($user->getId() === $this->authenticatedUserProvider->getUser()->getId()) {
            $e = new TranslatableUserException('cannotDeleteSelfUser');
            $e->setParameters(['username' => $user->getUsername()]);
            $e->setDomain('users');

            throw $e;
        }
    }
}
