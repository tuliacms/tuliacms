<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\Event;

use Tulia\Cms\Node\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class IntroductionChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $introduction;

    /**
     * @param AggregateId $nodeId
     * @param null|string $introduction
     */
    public function __construct(AggregateId $nodeId, ?string $introduction)
    {
        parent::__construct($nodeId);

        $this->introduction = $introduction;
    }

    /**
     * @return null|string
     */
    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }
}
