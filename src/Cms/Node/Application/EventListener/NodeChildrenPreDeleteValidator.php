<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Application\Event\NodePreDeleteEvent;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\Node\Application\Exception\TranslatableNodeException;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\ScopeEnum;

/**
 * This class is responsible to detect if deleting node has children.
 * If has, exception is thrown to prevent delete and prevent mismatch
 * in Database.
 *
 * @author Adam Banaszkiewicz
 */
class NodeChildrenPreDeleteValidator implements EventSubscriberInterface
{
    protected NodeFinderInterface $nodeFinder;

    public function __construct(NodeFinderInterface $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodePreDeleteEvent::class => ['handle', 1000],
        ];
    }

    public function handle(NodePreDeleteEvent $event): void
    {
        $nodes = $this->nodeFinder->find([
            'children_of' => $event->getNode()->getId(),
            'per_page'    => 1,
        ], ScopeEnum::INTERNAL);

        if ($nodes->count()) {
            $e = new TranslatableNodeException('cannotDeleteDueToContainingChildren');
            $e->setParameters(['name' => $event->getNode()->getTitle()]);
            $e->setDomain('validators');

            throw $e;
        }
    }
}
