<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

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
