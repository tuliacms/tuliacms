<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param ConnectionInterface $connection
     * @param EventDispatcherInterface $eventDispatcher
     * @param StorageInterface $storage
     */
    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        StorageInterface $storage
    ) {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->storage         = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->storage, array_merge([
            'scope' => $scope,
        ], $params));

        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }
}
