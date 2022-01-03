<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\NodeTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\AbstractNodeTypeProvider;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseProvider extends AbstractNodeTypeProvider
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->connection->fetchAllAssociative('SELECT * FROM #__node_type') as $type) {
            $type['layout'] = $type['code'] . '_layout';
            $type['fields'] = [];

            $result[] = $this->buildNodeType($type['code'], $type, false);
        }

        return $result;
    }
}
