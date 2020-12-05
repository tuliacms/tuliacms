<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Event;

use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class Renamed extends DomainEvent
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @param AggregateId $itemId
     * @param null|string $name
     */
    public function __construct(AggregateId $itemId, ?string $name)
    {
        parent::__construct($itemId);

        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
