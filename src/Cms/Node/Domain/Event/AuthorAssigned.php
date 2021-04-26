<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class AuthorAssigned extends DomainEvent
{
    /**
     * @var string
     */
    private $authorId;

    /**
     * @param AggregateId $nodeId
     * @param string $authorId
     */
    public function __construct(AggregateId $nodeId, string $authorId)
    {
        parent::__construct($nodeId);

        $this->authorId = $authorId;
    }

    /**
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }
}
