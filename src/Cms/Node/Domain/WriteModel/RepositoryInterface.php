<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;
use Tulia\Cms\Node\Domain\WriteModel\Aggregate\Node;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param AggregateId $id
     * @param string $locale
     *
     * @return Node
     *
     * @throws NodeNotFoundException
     */
    public function find(AggregateId $id, string $locale): Node;

    /**
     * @param Node $node
     */
    public function save(Node $node): void;

    /**
     * @param Node $node
     */
    public function delete(Node $node): void;
}
