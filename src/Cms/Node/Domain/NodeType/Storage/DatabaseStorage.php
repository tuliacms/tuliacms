<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType\Storage;

use Tulia\Cms\Node\Domain\NodeType\NodeType;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function all(): array
    {
        $sourceData = $this->connection->fetchAll('SELECT * FROM #__node_type WHERE active = 1');
        $types = [];

        foreach ($sourceData as $source) {
            $type = new NodeType($source['type']);
            $type->setTranslationDomain($source['translation_domain'] ?? 'pages');
            $type->setController($source['controller'] ?? NodeTypeInterface::CONTROLLER);
            $type->setIsRoutable((bool) ($source['is_routable']));

            if (empty($source['supports']) === false) {
                $type->setSupports(json_decode($source['supports'], true));
            }
            if (empty($source['parameters']) === false) {
                $type->setParameters(json_decode($source['parameters'], true));
            }

            //$type->addTaxonomy('category', 'category_id');
            //$type->addTaxonomy('tag', 'tags_ids', [ 'multiple' => true ]);

            $types[$source['type']] = $type;
        }

        return $types;
    }
}
