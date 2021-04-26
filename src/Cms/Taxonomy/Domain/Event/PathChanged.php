<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class PathChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $path;

    /**
     * @param AggregateId $termId
     * @param null|string $path
     */
    public function __construct(AggregateId $termId, ?string $path)
    {
        parent::__construct($termId);

        $this->path = $path;
    }

    /**
     * @return null|string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
