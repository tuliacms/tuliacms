<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\User\Query\Model\Collection;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\User\Query\Exception\MultipleFetchException;
use Tulia\Cms\User\Query\Exception\QueryException;
use Tulia\Cms\User\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderInterface
{
    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void;

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
     * @return Collection|User[]
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
     * @param string $username
     *
     * @return User|null
     *
     * @throws QueryException
     */
    public function findByUsername(string $username): ?User;

    /**
     * @param string $id
     *
     * @return User|null
     *
     * @throws QueryException
     */
    public function find(string $id): ?User;

    /**
     * @param string $email
     *
     * @return User|null
     *
     * @throws QueryException
     */
    public function findByEmail(string $email): ?User;
}
