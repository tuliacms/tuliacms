<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class ContentSourceChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $contentSource;

    /**
     * @param AggregateId $nodeId
     * @param null|string $contentSource
     */
    public function __construct(AggregateId $nodeId, ?string $contentSource)
    {
        parent::__construct($nodeId);

        $this->contentSource = $contentSource;
    }

    /**
     * @return null|string
     */
    public function getContentSource(): ?string
    {
        return $this->contentSource;
    }
}
