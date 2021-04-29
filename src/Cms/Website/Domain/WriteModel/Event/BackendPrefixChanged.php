<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Website\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class BackendPrefixChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $backendPrefix;

    public function __construct(AggregateId $id, string $backendPrefix)
    {
        parent::__construct($id);

        $this->backendPrefix = $backendPrefix;
    }

    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }
}
