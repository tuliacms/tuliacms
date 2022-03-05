<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query;

use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class QueryBuilder extends DoctrineQueryBuilder
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->connection = $connection;
    }

    public function delete($delete = null, $alias = null): DoctrineQueryBuilder
    {
        return parent::delete($this->connection->prepareTablePrefix($delete), $alias);
    }

    public function update($update = null, $alias = null): DoctrineQueryBuilder
    {
        return parent::update($this->connection->prepareTablePrefix($update), $alias);
    }

    public function insert($insert = null): DoctrineQueryBuilder
    {
        return parent::insert($this->connection->prepareTablePrefix($insert));
    }

    public function from($from, $alias = null): DoctrineQueryBuilder
    {
        return parent::from($this->connection->prepareTablePrefix($from), $alias);
    }

    public function join($fromAlias, $join, $alias, $condition = null): DoctrineQueryBuilder
    {
        return parent::join($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    public function innerJoin($fromAlias, $join, $alias, $condition = null): DoctrineQueryBuilder
    {
        return parent::innerJoin($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    public function leftJoin($fromAlias, $join, $alias, $condition = null): DoctrineQueryBuilder
    {
        return parent::leftJoin($this->connection->prepareTablePrefix($fromAlias), $join, $alias, $condition);
    }

    public function rightJoin($fromAlias, $join, $alias, $condition = null): DoctrineQueryBuilder
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
