<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Application\Event\NodePreDeleteEvent;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Application\Exception\TranslatableNodeException;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;

/**
 * This class is responsible to detect if deleting node has children.
 * If has, exception is thrown to prevent delete and prevent mismatch
 * in Database.
 *
 * @author Adam Banaszkiewicz
 */
class NodeChildrenPreDeleteValidator implements EventSubscriberInterface
{
    protected FinderFactoryInterface $finderFactory;

    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodePreDeleteEvent::class => ['handle', 1000],
        ];
    }

    /**
     * @param NodePreDeleteEvent $event
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws TranslatableNodeException
     */
    public function handle(NodePreDeleteEvent $event): void
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->setCriteria([
            'children_of' => $event->getNode()->getId(),
            'per_page'    => 1,
        ]);
        $finder->fetchRaw();

        if ($finder->getTotalCount()) {
            $e = new TranslatableNodeException('cannotDeleteDueToContainingChildren');
            $e->setParameters(['name' => $event->getNode()->getTitle()]);
            $e->setDomain('validators');

            throw $e;
        }
    }
}
