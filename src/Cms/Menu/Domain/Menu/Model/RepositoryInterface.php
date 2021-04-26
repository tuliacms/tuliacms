<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Model;

use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Menu;
use Tulia\Cms\Menu\Domain\Menu\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param AggregateId $id
     *
     * @throws MenuNotFoundException
     */
    public function find(AggregateId $id): Menu;

    /**
     * @param Menu $menu
     */
    public function save(Menu $menu): void;

    /**
     * @param Menu $menu
     */
    public function delete(Menu $menu): void;
}
