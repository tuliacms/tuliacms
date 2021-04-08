<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query;

use Tulia\Cms\User\Query\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderFactoryInterface
{
    /**
     * @param string $scope
     *
     * @return FinderInterface
     */
    public function getInstance(string $scope): FinderInterface;

    /**
     * @param string $scope
     * @param array $criteria
     *
     * @return Collection
     */
    public function fetch(string $scope, array $criteria): Collection;

    /**
     * @param string $scope
     * @param array $criteria
     *
     * @return Collection
     */
    public function fetchRaw(string $scope, array $criteria): Collection;
}
