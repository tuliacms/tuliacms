<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Metadata\Domain\ReadModel\MetadataFinder;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Taxonomy\Query\Model\Collection;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @var
     */
    protected $queryClass;
    private MetadataFinder $metadataFinder;

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        MetadataFinder $metadataFinder,
        string $queryClass = DbalQuery::class
    ) {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite  = $currentWebsite;
        $this->metadataFinder = $metadataFinder;
        $this->queryClass      = $queryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->metadataFinder, $this->queryClass, array_merge([
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
