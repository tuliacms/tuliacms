<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\User\Application\Event\UserPreDeleteEvent;
use Tulia\Cms\User\Application\Exception\TranslatableUserException;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SelfUserDeleteDetector implements EventSubscriberInterface
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserPreDeleteEvent::class => ['handle', 1000],
        ];
    }

    /**
     * @param UserPreDeleteEvent $event
     *
     * @throws TranslatableUserException
     */
    public function handle(UserPreDeleteEvent $event): void
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
