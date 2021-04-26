<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Collection;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;

/**
 * @author Adam Banaszkiewicz
 */
interface QueryInterface
{
    /**
     * @return array
     */
    public function getBaseQueryArray(): array;

    /**
     * @param array $query
     *
     * @return Collection
     *
     * @throws QueryException
     */
    public function query(array $query): Collection;

    /**
     * @param array $query
     *
     * @return Collection
     *
     * @throws QueryException
     */
    public function queryRaw(array $query): Collection;

    /**
     * @param array $query
     *
     * @return array
     */
    public function execute(array $query): array;
}
