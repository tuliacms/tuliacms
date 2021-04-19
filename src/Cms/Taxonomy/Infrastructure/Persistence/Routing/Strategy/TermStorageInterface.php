<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Routing\Strategy;

use PDO;
use Exception;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\AbstractQuery;
use Tulia\Framework\Database\Connection;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface TermStorageInterface
{
    public function find(string $termId, string $locale): ?array;
}
