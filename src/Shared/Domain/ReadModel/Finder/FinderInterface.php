<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\ReadModel\Finder;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    public function getScope(): ?string;
    public function setScope(?string $scope): void;
    public function setCriteria(array $criteria): void;
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
     * @return Collection
     * @throws QueryNotFetchedException
     */
    public function getResult(): Collection;

    /**
     * @return int
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function getTotalCount(): int;

    /**
     * @return int
     * @throws QueryNotFetchedException
     */
    public function count(): int;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
