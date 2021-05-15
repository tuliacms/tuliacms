<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected RegistryInterface $registry;

    protected ConnectionInterface $connection;

    protected EventDispatcherInterface $eventDispatcher;

    protected CurrentWebsiteInterface $currentWebsite;

    protected MetadataFinder $metadataFinder;

    protected string $queryClass;

    public function __construct(
        RegistryInterface $registry,
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        MetadataFinder $metadataFinder,
        string $queryClass = DbalQuery::class
    ) {
        $this->registry = $registry;
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite = $currentWebsite;
        $this->metadataFinder = $metadataFinder;
        $this->queryClass = $queryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->registry, $this->metadataFinder, $this->queryClass, array_merge([
            'website' => $this->currentWebsite->getId(),
            'locale'  => $this->currentWebsite->getLocale()->getCode(),
            'scope'   => $scope,
        ], $params));

        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(string $scope, array $criteria): Collection
    {
        $finder = $this->getInstance($scope);
        $finder->setCriteria($criteria);
        $finder->fetch();
        return $finder->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRaw(string $scope, array $criteria): Collection
    {
        $finder = $this->getInstance($scope);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();
        return $finder->getResult();
    }
}
