<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Ports\Domain\ReadModel;

use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface FileFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|File
     */
    public function findOne(array $criteria, string $scope);

    public function find(array $criteria, string $scope): Collection;
}
