<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Command;

use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Taxonomy\Application\Event\TermCreatedEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermDeletedEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreCreateEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreDeleteEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreUpdateEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermUpdatedEvent;
use Tulia\Cms\Taxonomy\Application\Model\Term as ApplicationTerm;
use Tulia\Cms\Taxonomy\Domain\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\RepositoryInterface;
use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;
use Tulia\Cms\Taxonomy\Domain\Aggregate\Term as Aggregate;

/**
 * @author Adam Banaszkiewicz
 */
class TermStorage
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

    public function save(ApplicationTerm $term): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new AggregateId($term->getId()), $term->getLocale());

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when node not exists, and perform proper action when node not exists.
            $aggregateExists = true;
        } catch (TermNotFoundException $exception) {
            $aggregate = new Aggregate(
                new AggregateId($term->getId()),
                $term->getType(),
                $term->getWebsiteId(),
                $term->getLocale()
            );
        }

        if ($aggregateExists) {
            $event = new TermPreUpdateEvent($term);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        } else {
            $event = new TermPreCreateEvent($term);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }

        $this->updateAggregate($term, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new TermUpdatedEvent($term));
        } else {
            $this->eventDispatcher->dispatch(new TermCreatedEvent($term));
        }
    }

    public function delete(ApplicationTerm $term): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($term->getId()), $term->getLocale());
        } catch (TermNotFoundException $exception) {
            return;
        }

        $event = new TermPreDeleteEvent($term);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new TermDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new TermDeletedEvent($term));
    }

    private function updateAggregate(ApplicationTerm $term, Aggregate $aggregate): void
    {
        foreach ($term->getMetadata() as $key => $val) {
            $aggregate->changeMetadataValue($key, $val);
        }

        $aggregate->changeSlug($term->getSlug());
        $aggregate->rename($term->getName());
        $aggregate->assignToParent($term->getParentId());
        $aggregate->changeVisibility($term->getVisibility());
    }
}
