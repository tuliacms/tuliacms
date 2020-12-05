<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Query;

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
}
