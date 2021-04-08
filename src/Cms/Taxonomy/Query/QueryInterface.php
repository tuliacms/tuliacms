<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query;

use Tulia\Cms\Taxonomy\Query\Model\Collection;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;

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
     * @return int
     */
    public function count(array $query): int;

    /**
     * @param array $query
     *
     * @return int
     */
    public function countRaw(array $query): int;

    /**
     * @param array $query
     *
     * @return array
     */
    public function execute(array $query): array;

    /**
     * @return int
     *
     * @throws QueryException
     */
    public function countFoundRows(): int;
}
