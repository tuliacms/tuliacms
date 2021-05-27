<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Ports\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Widget\Domain\ReadModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Widget
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
