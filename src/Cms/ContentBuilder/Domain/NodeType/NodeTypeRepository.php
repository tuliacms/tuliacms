<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeRepository
{
    private NodeTypeStorageInterface $nodeTypeStorage;

    /*public function __construct(NodeTypeStorageInterface $nodeTypeStorage)
    {
        $this->nodeTypeStorage = $nodeTypeStorage;
    }*/

    public function insert(NodeType $nodeType): void
    {

    }

    public function update(NodeType $nodeType): void
    {

    }

    public function delete(NodeType $nodeType): void
    {

    }
}
