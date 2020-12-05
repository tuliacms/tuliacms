<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;

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
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @var string
     */
    private $queryClass;

    /**
     * @param ConnectionInterface $connection
     * @param CurrentWebsiteInterface $currentWebsite
     * @param string $queryClass
     */
    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass
    ) {
        $this->connection     = $connection;
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
