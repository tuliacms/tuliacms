<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL;

use Closure;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Exception;
use Throwable;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface ConnectionInterface
{
    public const PARAM_ARRAY_INT = Connection::PARAM_INT_ARRAY;

    public const PARAM_ARRAY_STR = Connection::PARAM_STR_ARRAY;

    public const PARAM_INT = \PDO::PARAM_INT;

    public const PARAM_STR = \PDO::PARAM_STR;

    /**
     * @param $query
     * @param array $params
     * @param array $types
     * @param QueryCacheProfile|null $qcp
     *
     * @return mixed
     */
    public function executeQuery(
        string $sql,
        array $params = [],
        $types = [],
        ?QueryCacheProfile $qcp = null
    ): Result;

    public function executeUpdate(string $sql, array $params = [], array $types = []): int;

    public function query(string $sql): Result;

    public function exec(string $sql): int;

    public function prepare(string $sql): Statement;

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
     * @param array $types
     *
     * @return mixed
     */
    public function fetchFirstColumn($statement, array $params = [], array $types = []);

    /**
     * @param string $query
     * @param array $params
     * @param array $types
     *
     * @return mixed
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array;

    public function createQueryBuilder(): QueryBuilder;

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
