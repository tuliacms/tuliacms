<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Collection;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder implements FinderInterface
{
    protected ConnectionInterface $connection;
    protected array $params = [];
    protected array $criteria = [];
    protected array $fetchData = [
        'result' => null,
    ];
    protected ?QueryInterface $query = null;
    private string $queryClass;

    public function __construct(ConnectionInterface $connection, string $queryClass, array $params)
    {
        $this->connection = $connection;
        $this->queryClass = $queryClass;
        $this->params     = array_merge([
            'website' => null,
            'scope'   => 'default',
        ], $params);

        $this->fetchData['result'] = new Collection();
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
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRaw(): void
    {
        $this->prepareFetch();
        $this->fetchData['result'] = $this->query->queryRaw($this->criteria);
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

        $this->criteria['website'] = $this->criteria['website'] ?? $this->params['website'];
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
    public function find(string $id, array $query = []): ?Menu
    {
        $this->setCriteria(array_merge(['id' => $id], $query));
        $this->fetch();

        return $this->getResult()[0] ?? null;
    }

    protected function instantiateQuery(): QueryInterface
    {
        $classname = $this->queryClass;

        return new $classname($this->connection);
    }
}
