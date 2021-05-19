<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
interface TermFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Term
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
