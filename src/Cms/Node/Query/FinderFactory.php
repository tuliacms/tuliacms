<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

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
     * @var string
     */
    protected $queryClass;

    /**
     * @param RegistryInterface $registry
     * @param ConnectionInterface $connection
     * @param EventDispatcherInterface $eventDispatcher
     * @param CurrentWebsiteInterface $currentWebsite
     * @param string $queryClass
     */
    public function __construct(
        RegistryInterface $registry,
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass
    ) {
        $this->registry        = $registry;
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite  = $currentWebsite;
        $this->queryClass      = $queryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->registry, $this->queryClass, array_merge([
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
