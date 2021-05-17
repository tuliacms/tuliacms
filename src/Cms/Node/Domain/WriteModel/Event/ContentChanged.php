<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class ContentChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $content;

    /**
     * @param AggregateId $nodeId
     * @param null|string $content
     */
    public function __construct(AggregateId $nodeId, ?string $content)
    {
        parent::__construct($nodeId);

        $this->content = $content;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
}
