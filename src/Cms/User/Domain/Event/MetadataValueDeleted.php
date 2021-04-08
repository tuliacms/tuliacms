<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\Event;

use Tulia\Cms\User\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataValueDeleted extends DomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param AggregateId $nodeId
     * @param string $name
     * @param mixed $value
     */
    public function __construct(AggregateId $nodeId, string $name, $value)
    {
        parent::__construct($nodeId);

        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
