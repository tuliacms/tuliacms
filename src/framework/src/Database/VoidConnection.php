<?php

declare(strict_types=1);

namespace Tulia\Framework\Database;

use Closure;
use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Tulia\Framework\Database\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class VoidConnection extends DoctrineConnection implements ConnectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function executeUpdate($query, array $params = [], array $types = [])
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        $statement = $this->prepareTablePrefix($statement);

        return parent::prepare($statement);
    }

    /**
     * {@inheritdoc}
     */
    public function update($tableExpression, array $data, array $identifier, array $types = [])
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableExpression, array $data, array $types = [])
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($tableExpression, array $identifier, array $types = [])
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement, array $params = [], array $types = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($sql, array $params = [], $types = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function transactional(Closure $func)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function rollBack(): void
    {

    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function prepareTablePrefix(string $query): string
    {
        return str_replace('#__', $_ENV['DATABASE_PREFIX'], $query);
    }
}
