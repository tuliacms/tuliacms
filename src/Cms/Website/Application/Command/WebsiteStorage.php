<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Command;

use Tulia\Cms\Website\Application\Event\WebsiteCreatedEvent;
use Tulia\Cms\Website\Application\Event\WebsiteDeletedEvent;
use Tulia\Cms\Website\Application\Event\WebsitePreCreateEvent;
use Tulia\Cms\Website\Application\Event\WebsitePreDeleteEvent;
use Tulia\Cms\Website\Application\Event\WebsitePreUpdateEvent;
use Tulia\Cms\Website\Application\Event\WebsiteUpdatedEvent;
use Tulia\Cms\Website\Application\Model\Locale as ApplicationLocale;
use Tulia\Cms\Website\Application\Model\Website as ApplicationWebsite;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Website as Aggregate;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Locale as AggregateLocale;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Repository;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteStorage
{
    private Repository $repository;
    private EventBusInterface $eventDispatcher;

    public function __construct(Repository $repository, EventBusInterface $eventDispatcher)
    {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(ApplicationWebsite $website): void
    {
        $locales = [];

        /** @var ApplicationLocale $locale */
        foreach ($website->getLocales() as $locale) {
            $locales[] = $locale->produceAggregate();
        }

        $aggregate = new Aggregate(
            new AggregateId($website->getId()),
            $website->getName(),
            $website->getBackendPrefix(),
            new LocaleCollection($locales)
        );

        $event = new WebsitePreCreateEvent($website);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->updateAggregate($website, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());
        $this->eventDispatcher->dispatch(new WebsiteCreatedEvent($website));
    }

    public function update(ApplicationWebsite $website): void
    {
        $aggregate = $this->repository->find(new AggregateId($website->getId()));

        $event = new WebsitePreUpdateEvent($website);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->updateAggregate($website, $aggregate);

        $this->repository->update($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());
        $this->eventDispatcher->dispatch(new WebsiteUpdatedEvent($website));
    }

    public function delete(ApplicationWebsite $website): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($website->getId()));
        } catch (WebsiteNotFoundException $exception) {
            return;
        }

        $event = new WebsitePreDeleteEvent($website);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new WebsiteDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new WebsiteDeletedEvent($website));
    }

    private function updateAggregate(ApplicationWebsite $website, Aggregate $aggregate): void
    {
        $aggregate->rename($website->getName());
        $aggregate->changeBackendPrefix($website->getBackendPrefix());

        $this->addNewLocales($website, $aggregate);
        $this->updateExistingLocales($website, $aggregate);
        $this->removeOrphanLocales($website, $aggregate);
    }

    private function addNewLocales(ApplicationWebsite $website, Aggregate $aggregate): void
    {
        /** @var ApplicationLocale $locale */
        foreach ($website->getLocales() as $locale) {
            if ($aggregate->hasLocaleByCode($locale->getCode()) === false) {
                $aggregate->addLocale($locale->produceAggregate());
            }
        }
    }

    private function updateExistingLocales(ApplicationWebsite $website, Aggregate $aggregate): void
    {
        /** @var ApplicationLocale $locale */
        foreach ($website->getLocales() as $locale) {
            if ($aggregate->hasLocaleByCode($locale->getCode())) {
                $aggregate->updateLocale($locale->produceAggregate());
            }
        }
    }

    private function removeOrphanLocales(ApplicationWebsite $website, Aggregate $aggregate): void
    {
        /** @var AggregateLocale $locale */
        foreach ($aggregate->getLocales() as $locale) {
            if ($website->hasLocaleByCode($locale->getCode()) === false) {
                $aggregate->removeLocale($locale);
            }
        }
    }
}
