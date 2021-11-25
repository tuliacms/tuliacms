<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\NodeTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeProviderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Connection;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseProvider implements NodeTypeProviderInterface
{
    private Connection $connection;

    /*public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }*/

    public function provide(): array
    {
        return [];
    }
}
