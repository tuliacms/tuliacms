<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Collection;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;

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
     * @return Collection
     *
     * @throws QueryNotFetchedException
     */
    public function getResult(): Collection;

    /**
     * @param string $id
     * @param array $query
     *
     * @return Menu|null
     *
     * @throws QueryException
     */
    public function find(string $id, array $query = []): ?Menu;
}
