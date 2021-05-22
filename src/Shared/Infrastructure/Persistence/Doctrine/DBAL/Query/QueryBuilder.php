<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query;

use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    private ConnectionInterface $connection;

    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($delete = null, $alias = null)
    {
        return parent::delete($this->connection->prepareTablePrefix($delete), $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function update($update = null, $alias = null)
    {
        return parent::update($this->connection->prepareTablePrefix($update), $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($insert = null)
    {
        return parent::insert($this->connection->prepareTablePrefix($insert));
    }

    /**
     * {@inheritdoc}
     */
    public function from($from, $alias = null)
    {
        return parent::from($this->connection->prepareTablePrefix($from), $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function join($fromAlias, $join, $alias, $condition = null)
    {
        return parent::join($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function innerJoin($fromAlias, $join, $alias, $condition = null)
    {
        return parent::innerJoin($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function leftJoin($fromAlias, $join, $alias, $condition = null)
    {
        return parent::leftJoin($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function rightJoin($fromAlias, $join, $alias, $condition = null)
    {
        return parent::rightJoin($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    public function compileSQL(): string
    {
        $sql = $this->getSQL();

        foreach ($this->getParameters() as $parameter => $value) {
            if (is_numeric($value)) {
                $sql = str_replace(":$parameter", $value, $sql);
            } elseif ($value === null) {
                $sql = str_replace(":$parameter", 'NULL', $sql);
            } else{
                $sql = str_replace(":$parameter", $this->connection->quote($value), $sql);
            }
        }

        return $sql;
    }
}
