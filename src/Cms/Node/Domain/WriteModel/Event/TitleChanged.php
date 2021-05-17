<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class TitleChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $title;

    /**
     * @param AggregateId $nodeId
     * @param null|string $title
     */
    public function __construct(AggregateId $nodeId, ?string $title)
    {
        parent::__construct($nodeId);

        $this->title = $title;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
