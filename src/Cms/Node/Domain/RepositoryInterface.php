<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;
use Tulia\Cms\Node\Domain\Aggregate\Node;
use Tulia\Cms\Node\Domain\Exception\NodeNotFoundException;

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
