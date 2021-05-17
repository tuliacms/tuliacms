<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class MovedToLevel extends DomainEvent
{
    /**
     * @var int
     */
    private $level;

    /**
     * @param AggregateId $nodeId
     * @param int $level
     */
    public function __construct(AggregateId $nodeId, int $level)
    {
        parent::__construct($nodeId);

        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
