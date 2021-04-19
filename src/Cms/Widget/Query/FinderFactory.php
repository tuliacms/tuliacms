<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Widget\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected ConnectionInterface $connection;
    protected EventDispatcherInterface $eventDispatcher;
    protected CurrentWebsiteInterface $currentWebsite;
    protected string $queryClass;

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass = DbalQuery::class
    ) {
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
        $finder = new Finder($this->connection, $this->queryClass, array_merge([
            'website' => $this->currentWebsite->getId(),
            'locale'  => $this->currentWebsite->getLocale()->getCode(),
            'scope'   => $scope,
        ], $params));

        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }
}
