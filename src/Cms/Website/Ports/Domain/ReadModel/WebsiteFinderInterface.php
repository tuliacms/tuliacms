<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Ports\Domain\ReadModel;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Website
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
