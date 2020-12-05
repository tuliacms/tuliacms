<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class BackendPrefixChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $backendPrefix;

    /**
     * @param AggregateId $id
     * @param string $backendPrefix
     */
    public function __construct(AggregateId $id, string $backendPrefix)
    {
        parent::__construct($id);

        $this->backendPrefix = $backendPrefix;
    }

    /**
     * @return string
     */
    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }
}
