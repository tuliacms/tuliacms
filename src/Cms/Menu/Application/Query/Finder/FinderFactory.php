<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected ConnectionInterface $connection;
    protected CurrentWebsiteInterface $currentWebsite;
    private string $queryClass;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass
    ) {
        $this->connection = $connection;
        $this->currentWebsite = $currentWebsite;
        $this->queryClass = $queryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->queryClass, array_merge([
            'website' => $this->currentWebsite->getId(),
            'scope'   => $scope,
        ], $params));

        return $finder;
    }
}
