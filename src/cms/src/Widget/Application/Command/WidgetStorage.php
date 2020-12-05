<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\Command;

use Tulia\Cms\Widget\Application\Event\WidgetCreatedEvent;
use Tulia\Cms\Widget\Application\Event\WidgetDeletedEvent;
use Tulia\Cms\Widget\Application\Event\WidgetPreCreateEvent;
use Tulia\Cms\Widget\Application\Event\WidgetPreDeleteEvent;
use Tulia\Cms\Widget\Application\Event\WidgetPreUpdateEvent;
use Tulia\Cms\Widget\Application\Event\WidgetUpdatedEvent;
use Tulia\Cms\Widget\Application\Model\Widget as ApplicationWidget;
use Tulia\Cms\Widget\Domain\Event\WidgetDeleted;
use Tulia\Cms\Widget\Domain\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\RepositoryInterface;
use Tulia\Cms\Widget\Domain\Aggregate\Widget as Aggregate;
use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetStorage
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

    public function save(ApplicationWidget $widget): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new AggregateId($widget->getId()), $widget->getLocale());

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when widget not exists, and perform proper action when widget not exists.
            $aggregateExists = true;
        } catch (WidgetNotFoundException $exception) {
            $aggregate = new Aggregate(
                new AggregateId($widget->getId()),
                $widget->getWidgetId(),
                $widget->getWebsiteId(),
                $widget->getLocale()
            );
        }

        if ($aggregateExists) {
            $event = new WidgetPreUpdateEvent($widget);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        } else {
            $event = new WidgetPreCreateEvent($widget);
            $this->eventDispatcher->dispatch($event);

            if ($event->isPropagationStopped()) {
                return;
            }
        }

        $this->updateAggregate($widget, $aggregate);
        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new WidgetUpdatedEvent($widget));
        } else {
            $this->eventDispatcher->dispatch(new WidgetCreatedEvent($widget));
        }
    }

    public function delete(ApplicationWidget $widget): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($widget->getId()), $widget->getLocale());
        } catch (WidgetNotFoundException $exception) {
            return;
        }

        $event = new WidgetPreDeleteEvent($widget);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new WidgetDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new WidgetDeletedEvent($widget));
    }

    private function updateAggregate(ApplicationWidget $widget, Aggregate $aggregate): void
    {
        $config = $widget->getWidgetConfiguration();

        $aggregate->moveToSpace($widget->getSpace());
        $aggregate->rename($widget->getName());
        $aggregate->changeHtmlClass($widget->getHtmlClass());
        $aggregate->changeHtmlId($widget->getHtmlId());
        $aggregate->persistStyles($widget->getStyles());
        $aggregate->updatePayload($config->allNotMultilingual());
        $aggregate->updateLocalizedPayload($config->allMultilingual());
        $aggregate->changeVisibility($widget->getVisibility());
        $aggregate->changeTitle($widget->getTitle());
    }
}
