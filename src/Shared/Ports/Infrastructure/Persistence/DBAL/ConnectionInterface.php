<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL;

use Closure;
use Exception;
use Throwable;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;

/**
 * @author Adam Banaszkiewicz
 */
interface ConnectionInterface
{
    public const PARAM_ARRAY_INT = Connection::PARAM_INT_ARRAY;
    public const PARAM_ARRAY_STR = Connection::PARAM_STR_ARRAY;

    /**
     * @param $query
     * @param array $params
     * @param array $types
     * @param QueryCacheProfile|null $qcp
     *
     * @return mixed
     */
    public function executeQuery($query, array $params = [], $types = [], ?QueryCacheProfile $qcp = null);

    /**
     * @param $query
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function executeUpdate($query, array $params = [], array $types = []);
    public function query();

    /**
     * @param $statement
     *
     * @return mixed
     */
    public function prepare($statement);

    /**
     * @param $tableExpression
     * @param array $data
     * @param array $identifier
     * @param array $types
     *
     * @return mixed
     */
    public function update($tableExpression, array $data, array $identifier, array $types = []);

    /**
     * @param $tableExpression
     * @param array $data
     * @param array $types
     *
     * @return mixed
     */
    public function insert($tableExpression, array $data, array $types = []);

    /**
     * @param $tableExpression
     * @param array $identifier
     * @param array $types
     *
     * @return mixed
     */
    public function delete($tableExpression, array $identifier, array $types = []);

    /**
     * @param $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchAssoc($statement, array $params = [], array $types = []);

    /**
     * @param $statement
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchArray($statement, array $params = [], array $types = []);

    /**
     * @param $statement
     * @param array $params
     * @param int $column
     * @param array $types
     *
     * @return mixed
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = []);

    /**
     * @param string $query
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array;

    public function createQueryBuilder();

    /**
     * @param string $query
     *
     * @return string
     */
    public function prepareTablePrefix(string $query): string;

    /**
     * @param Closure $func
     *
     * @throws Exception
     * @throws Throwable
     */
    public function transactional(Closure $func);

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
