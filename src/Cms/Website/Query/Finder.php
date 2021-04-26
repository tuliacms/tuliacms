<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Query\Model\Collection;
use Tulia\Cms\Website\Query\Model\CollectionInterface;
use Tulia\Cms\Website\Query\Event\QueryFilterEvent;
use Tulia\Cms\Website\Query\Event\QueryPrepareEvent;
use Tulia\Cms\Website\Query\Exception\MultipleFetchException;
use Tulia\Cms\Website\Query\Exception\QueryNotFetchedException;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder implements FinderInterface
{
    protected ConnectionInterface $connection;
    protected StorageInterface $storage;
    protected array $params = [];
    protected array $criteria = [];
    protected array $fetchData = [
        'result'      => null,
        'total_count' => null,
    ];
    protected ?Query $query = null;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(ConnectionInterface $connection, StorageInterface $storage, array $params)
    {
        $this->connection = $connection;
        $this->storage    = $storage;
        $this->params     = array_merge([
            'scope' => 'default',
        ], $params);

        $this->fetchData['result'] = new Collection();
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope(): ?string
    {
        return $this->params['scope'];
    }

    /**
     * {@inheritdoc}
     */
    public function setScope(?string $scope): void
    {
        $this->params['scope'] = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function setCriteria(array $criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyCriteria(array $criteria): void
    {
        $this->criteria = array_merge($this->criteria, $criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(): void
    {
        $this->prepareFetch();
        $this->fetchData['result'] = $this->query->query($this->criteria);
        $this->afterQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRaw(): void
    {
        $this->prepareFetch();
        $this->fetchData['result'] = $this->query->queryRaw($this->criteria);
        $this->afterQuery();
    }

    /**
     * @throws MultipleFetchException
     */
    protected function prepareFetch(): void
    {
        if ($this->query instanceof Query) {
            throw new MultipleFetchException('Cannot fetch again. Query already done and You can get results from finder. For new query please use fresh finder.');
        }

        $this->query = new Query($this->connection->createQueryBuilder(), $this->storage);

        $this->criteria['count_found_rows'] = true;

        if ($this->eventDispatcher) {
            $event = new QueryPrepareEvent($this->criteria, $this->getScope());
            $this->eventDispatcher->dispatch($event);

            $this->setCriteria($event->getCriteria());
            $this->setScope($event->getScope());
        }
    }

    protected function afterQuery(): void
    {
        if (!$this->eventDispatcher) {
            return;
        }

        $event = new QueryFilterEvent($this->fetchData['result'], $this->getScope());
        $this->eventDispatcher->dispatch($event);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(): CollectionInterface
    {
        if (! $this->query instanceof Query) {
            throw new QueryNotFetchedException('Cannot get total count, fetch() method must be called first.');
        }

        return $this->fetchData['result'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        if ($this->fetchData['total_count']) {
            return $this->fetchData['total_count'];
        }

        if (! $this->query instanceof Query) {
            throw new QueryNotFetchedException('Cannot get total count, fetch() method must be called first.');
        }

        return $this->fetchData['total_count'] = $this->query->countFoundRows();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        if (! $this->query instanceof Query) {
            throw new QueryNotFetchedException('Cannot count results, fetch() method must be called first.');
        }

        return $this->fetchData['result']->count();
    }
}
