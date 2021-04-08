<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataValueChanged extends DomainEvent
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
     * @param AggregateId $termId
     * @param string $name
     * @param mixed $value
     */
    public function __construct(AggregateId $termId, string $name, $value)
    {
        parent::__construct($termId);

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
