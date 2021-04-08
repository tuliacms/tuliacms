<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Node\Application\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class NodeEvent extends Event
{
    /**
     * @var Node
     */
    protected $node;

    /**
     * @param Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Node
     */
    public function getNode(): Node
    {
        return $this->node;
    }
}
