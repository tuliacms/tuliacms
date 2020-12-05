<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Website\Query\Model\CollectionInterface;
use Tulia\Cms\Website\Query\Exception\MultipleFetchException;
use Tulia\Cms\Website\Query\Exception\QueryException;
use Tulia\Cms\Website\Query\Exception\QueryNotFetchedException;

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
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
