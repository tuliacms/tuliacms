<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Activity\Application\Command\ActivityStorage;
use Tulia\Cms\Activity\Application\Model\Row;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityLogger implements EventSubscriberInterface
{
    protected ActivityStorage $activity;
    protected AuthenticatedUserProviderInterface $userProvider;
    protected RouterInterface $router;
    protected RegistryInterface $registry;

    public function __construct(
        ActivityStorage $activity,
        AuthenticatedUserProviderInterface $userProvider,
        RouterInterface $router,
        RegistryInterface $registry
    ) {
        $this->activity     = $activity;
        $this->userProvider = $userProvider;
        $this->router       = $router;
        $this->registry     = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodeCreatedEvent::class => ['handleCreated', 0],
            NodeDeletedEvent::class => ['handleDeleted', 0],
        ];
    }

    public function handleCreated(NodeCreatedEvent $event): void
    {
        $this->log('activityUserCreatedNewNode', $event->getNode());
    }

    public function handleDeleted(NodeDeletedEvent $event): void
    {
        $this->log('activityUserDeletedNode', $event->getNode());
    }

    private function log(string $message, Node $node): void
    {
        $user = $this->userProvider->getUser();
        $type = $this->registry->getType($node->getType());

        $userLink = $this->router->generate('backend.user.edit', ['id' => $user->getId()]);

        $row = new Row();
        $row->setMessage($message);
        $row->setTranslationDomain($type->getTranslationDomain());
        $row->setContext([
            'username' => '<a href="' . $userLink . '">' . ($user->hasMeta('name') ? $user->getMeta('name') : $user->getUsername()) . '</a>',
            'link'     => $this->router->generate('backend.node.edit', ['id' => $node->getId(), 'node_type' => $node->getType()]),
        ]);

        $this->activity->save($row);
    }
}
