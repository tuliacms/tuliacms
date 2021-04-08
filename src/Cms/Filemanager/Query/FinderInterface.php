<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Filemanager\CollectionInterface;
use Tulia\Cms\Filemanager\FileInterface;
use Tulia\Cms\Filemanager\Exception\MultipleFetchException;
use Tulia\Cms\Filemanager\Exception\QueryException;
use Tulia\Cms\Filemanager\Exception\QueryNotFetchedException;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    /**
     * @return string|null
     */
    public function getScope(): ?string;

    /**
     * @param string|null $scope
     */
    public function setScope(?string $scope): void;

    /**
     * @param array $criteria
     */
    public function setCriteria(array $criteria): void;

    /**
     * @param array $criteria
     */
    public function modifyCriteria(array $criteria): void;

    /**
     * @throws MultipleFetchException
     * @throws QueryException
     */
    public function fetch(): void;

    /**
     * @throws MultipleFetchException
     * @throws QueryException
     */
    public function fetchRaw(): void;

    /**
     * @return CollectionInterface
     *
     * @throws QueryNotFetchedException
     */
    public function getResult(): CollectionInterface;

    /**
     * @return int
     *
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function getTotalCount(): int;

    /**
     * @return int
     *
     * @throws QueryNotFetchedException
     */
    public function count(): int;

    /**
     * @param string $id
     * @param string|null $type
     *
     * @return FileInterface|null
     */
    public function find(string $id, string $type = null): ?FileInterface;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
