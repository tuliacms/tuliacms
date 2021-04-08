<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Activity\Application\Command\ActivityStorage;
use Tulia\Cms\Activity\Application\Model\Row;
use Tulia\Cms\Node\Application\Event\NodeCreatedEvent;
use Tulia\Cms\Node\Application\Event\NodeDeletedEvent;
use Tulia\Cms\Node\Application\Model\Node;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityLogger
{
    /**
     * @var ActivityStorage
     */
    protected $activity;

    /**
     * @var AuthenticatedUserProviderInterface
     */
    protected $userProvider;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param ActivityStorage $activity
     * @param AuthenticatedUserProviderInterface $userProvider
     * @param RouterInterface $router
     * @param RegistryInterface $registry
     */
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

    /**
     * @param NodeCreatedEvent $event
     */
    public function handleCreated(NodeCreatedEvent $event): void
    {
        $this->log('activityUserCreatedNewNode', $event->getNode());
    }

    /**
     * @param NodeDeletedEvent $event
     */
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
