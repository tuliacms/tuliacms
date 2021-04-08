<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query\Factory;

use Tulia\Cms\Node\Query\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeFactoryInterface
{
    /**
     * Creates new NodeInterface object, with loaded metadata object (ready to sync),
     * and with default values given in $data array. Sets also object ID and is ready to
     * store in database.
     *
     * @param array $data
     *
     * @return Node
     */
    public function createNew(array $data = []): Node;
}
