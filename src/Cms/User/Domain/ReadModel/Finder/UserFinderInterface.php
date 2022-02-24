<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\ReadModel\Finder;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\User\Domain\ReadModel\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
interface UserFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|User
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
