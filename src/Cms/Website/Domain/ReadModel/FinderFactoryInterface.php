<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel;

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
