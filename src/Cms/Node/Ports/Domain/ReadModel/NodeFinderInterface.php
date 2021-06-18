<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Ports\Domain\ReadModel;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Node
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
