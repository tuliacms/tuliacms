<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\Event\QueryFilterEvent;
use Tulia\Cms\Node\Query\Event\QueryPrepareEvent;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Finder implements FinderInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var string
     */
    protected $queryClass;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * @var array
     */
    protected $fetchData = [
        'result'      => null,
        'total_count' => null,
    ];

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    protected MetadataFinder $metadataFinder;

    /**
     * @param ConnectionInterface $connection
     * @param RegistryInterface $registry
     * @param string $queryClass
     * @param array $params
     */
    public function __construct(
        ConnectionInterface $connection,
        RegistryInterface $registry,
        MetadataFinder $metadataFinder,
        string $queryClass,
        array $params
    ) {
        $this->connection = $connection;
        $this->registry   = $registry;
        $this->queryClass = $queryClass;
        $this->params     = array_merge([
            'website' => null,
            'locale'  => 'en_US',
            'scope'   => 'default',
        ], $params);

        $this->fetchData['result'] = new Collection();
        $this->metadataFinder = $metadataFinder;
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
        if ($this->query instanceof QueryInterface) {
            throw new MultipleFetchException('Cannot fetch again. Query already done and You can get results from finder. For new query please use fresh finder.');
        }

        $this->query = $this->instantiateQuery();

        $this->criteria['locale'] = $this->criteria['locale'] ?? $this->params['locale'];
        $this->criteria['website'] = $this->criteria['website'] ?? $this->params['website'];
        $this->criteria['count_found_rows'] = true;

        if (isset($this->criteria['order_hierarchical']) === false && isset($this->criteria['node_type'])) {
            $nodeType = $this->registry->getType($this->criteria['node_type']);

            if ($nodeType) {
                $this->criteria['order_hierarchical'] = $nodeType->supports('hierarchy');
            }
        }

        if ($this->eventDispatcher) {
            $event = new QueryPrepareEvent($this->criteria, $this->getScope());
            $this->eventDispatcher->dispatch($event);

            $this->setCriteria($event->getCriteria());
            $this->setScope($event->getScope());
        }
    }

    protected function instantiateQuery(): QueryInterface
    {
        $classname = $this->queryClass;

        return new $classname($this->connection->createQueryBuilder(), $this->metadataFinder);
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
    public function getResult(): Collection
    {
        if (! $this->query instanceof QueryInterface) {
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

        if (! $this->query instanceof QueryInterface) {
            throw new QueryNotFetchedException('Cannot get total count, fetch() method must be called first.');
        }

        return $this->fetchData['total_count'] = $this->query->countFoundRows();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        if (! $this->query instanceof QueryInterface) {
            throw new QueryNotFetchedException('Cannot count results, fetch() method must be called first.');
        }

        return $this->fetchData['result']->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginator(Request $request): Paginator
    {
        $perPage = $this->criteria['per_page'] ?? 15;

        return new Paginator($request, $this->getTotalCount(), null, $perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $id): ?Node
    {
        $this->setCriteria([
            'id'         => $id,
            'node_type'  => null,
            'order_by'   => null,
            'order_dir'  => null,
            'per_page'   => 1,
        ]);
        $this->fetch();

        return $this->getResult()[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug(string $slug): ?Node
    {
        $this->setCriteria([
            'slug'       => $slug,
            'node_type'  => null,
            'order_by'   => null,
            'order_dir'  => null,
            'per_page'   => 1,
        ]);
        $this->fetch();

        return $this->getResult()[0] ?? null;
    }
}
