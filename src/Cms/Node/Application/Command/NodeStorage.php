<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\Command;

use Tulia\Cms\Node\Application\Event\NodeCreatedEvent;
use Tulia\Cms\Node\Application\Event\NodeDeletedEvent;
use Tulia\Cms\Node\Application\Event\NodePreCreateEvent;
use Tulia\Cms\Node\Application\Event\NodePreDeleteEvent;
use Tulia\Cms\Node\Application\Event\NodePreUpdateEvent;
use Tulia\Cms\Node\Application\Event\NodeUpdatedEvent;
use Tulia\Cms\Node\Application\Model\Node as ApplicationNode;
use Tulia\Cms\Node\Domain\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\RepositoryInterface;
use Tulia\Cms\Node\Domain\Aggregate\Node as Aggregate;
use Tulia\Cms\Node\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Domain\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeStorage
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var EventBusInterface
     */
    private $eventDispatcher;

    /**
     * @param RepositoryInterface $repository
     * @param EventBusInterface $eventDispatcher
     */
    public function __construct(RepositoryInterface $repository, EventBusInterface $eventDispatcher)
    {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(ApplicationNode $node): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new AggregateId($node->getId()), $node->getLocale());

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when node not exists, and perform proper action when node not exists.
            $aggregateExists = true;
        } catch (NodeNotFoundException $exception) {
            $aggregate = new Aggregate(
                new AggregateId($node->getId()),
                $node->getType(),
                $node->getWebsiteId(),
                $node->getLocale()
            );
        }

        if ($aggregateExists) {
            $event = new NodePreUpdateEvent($node);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        } else {
            $event = new NodePreCreateEvent($node);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }

        $this->updateAggregate($node, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new NodeUpdatedEvent($node));
        } else {
            $this->eventDispatcher->dispatch(new NodeCreatedEvent($node));
        }
    }

    public function delete(ApplicationNode $node): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($node->getId()), $node->getLocale());
        } catch (NodeNotFoundException $exception) {
            return;
        }

        $event = new NodePreDeleteEvent($node);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new NodeDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new NodeDeletedEvent($node));
    }

    private function updateAggregate(ApplicationNode $node, Aggregate $aggregate): void
    {
        foreach ($node->getMetadata() as $key => $val) {
            $aggregate->changeMetadataValue($key, $val);
        }

        $aggregate->changePublicationPeriod(
            ImmutableDateTime::createFromMutable($node->getPublishedAt()),
            $node->getPublishedTo()
                ? ImmutableDateTime::createFromMutable($node->getPublishedTo())
                : null
        );
        $aggregate->changePublicationStatus($node->getStatus());
        $aggregate->assignAuthor($node->getAuthorId());
        $aggregate->changeSlug($node->getSlug());
        $aggregate->changeTitle($node->getTitle());
        $aggregate->changeContent($node->getContent());
        $aggregate->changeContentSource($node->getContentSource());
        $aggregate->changeIntroduction($node->getIntroduction());
        $aggregate->moveToLevel($node->getLevel());
        $aggregate->assignToParent($node->getParentId());
        $aggregate->categorize($node->getCategory());
    }
}
