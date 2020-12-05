<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteRenamed extends DomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param AggregateId $id
     * @param string $name
     */
    public function __construct(AggregateId $id, string $name)
    {
        parent::__construct($id);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
