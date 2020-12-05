<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Taxonomy\Query\Model\Collection;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\Taxonomy\Query\Exception\MultipleFetchException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;
use Tulia\Framework\Http\Request;

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
     * @param Request $request
     *
     * @return Paginator
     *
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function getPaginator(Request $request): Paginator;

    /**
     * @param string $id
     *
     * @return Term|null
     *
     * @throws QueryException
     */
    public function find(string $id): ?Term;

    /**
     * @param string $slug
     *
     * @return Term|null
     *
     * @throws QueryException
     */
    public function findBySlug(string $slug): ?Term;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;
}
