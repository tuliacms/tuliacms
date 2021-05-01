<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class InactiveWebsiteResolver implements EventSubscriberInterface
{
    protected CurrentWebsiteInterface $currentWebsite;
    private ?AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        CurrentWebsiteInterface $currentWebsite,
        ?AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->currentWebsite = $currentWebsite;
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['handle', 6],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        if ($this->authorizationChecker === null) {
            return;
        }

        if ($this->currentWebsite->isActive() === false && $this->authorizationChecker->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedHttpException(sprintf('Access denied, this website not exists.'));
        }
    }
}
