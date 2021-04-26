<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL;

use CLosure;
use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\DBALException;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Schema\SchemaManager;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Connection extends DoctrineConnection implements ConnectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
    {
        $query = $this->prepareTablePrefix($query);

        return parent::executeQuery($query, $params, $types, $qcp);
    }

    /**
     * {@inheritdoc}
     */
    public function executeUpdate($query, array $params = [], array $types = [])
    {
        $query = $this->prepareTablePrefix($query);

        return parent::executeUpdate($query, $params, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $args = func_get_args();
        $args[0] = $this->prepareTablePrefix($args[0]);

        return parent::query(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function exec($sql)
    {
        return parent::exec($this->prepareTablePrefix($sql));
    }

    /**
     * {@inheritdoc}
     */
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        return parent::executeStatement($this->prepareTablePrefix($sql), $params, $types);
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
        $tableExpression = $this->prepareTablePrefix($tableExpression);

        return parent::update($tableExpression, $data, $identifier, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableExpression, array $data, array $types = [])
    {
        $tableExpression = $this->prepareTablePrefix($tableExpression);

        return parent::insert($tableExpression, $data, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($tableExpression, array $identifier, array $types = [])
    {
        $tableExpression = $this->prepareTablePrefix($tableExpression);

        return parent::delete($tableExpression, $identifier, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        $statement = $this->prepareTablePrefix($statement);

        return parent::fetchAssoc($statement, $params, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement, array $params = [], array $types = [])
    {
        $statement = $this->prepareTablePrefix($statement);

        return parent::fetchArray($statement, $params, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        $statement = $this->prepareTablePrefix($statement);

        return parent::fetchColumn($statement, $params, $column, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        $query = $this->prepareTablePrefix($query);

        return parent::fetchAllAssociative($query, $params, $types);
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
        return parent::transactional($func);
    }


    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
        parent::beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
        parent::commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack(): void
    {
        parent::rollBack();
    }


    /**
     * @param string $query
     *
     * @return string
     */
    public function prepareTablePrefix(string $query): string
    {
        if (! isset($_ENV['DATABASE_PREFIX'])) {
            throw new \RuntimeException('Missing DATABASE_PREFIX env variable. Did You forget to define it in .env file?');
        }

        return str_replace('#__', $_ENV['DATABASE_PREFIX'], $query);
    }

    /**
     * @return SchemaManager
     *
     * @throws DBALException
     */
    public function getSchemaManager(): SchemaManager
    {
        return new SchemaManager($this, parent::getSchemaManager(), $this->getDatabasePlatform());
    }
}
