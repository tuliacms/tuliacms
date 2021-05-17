<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\Event;

use Tulia\Cms\Node\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class NodeCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param AggregateId $nodeId
     * @param string $type
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(AggregateId $nodeId, string $type, string $websiteId, string $locale)
    {
        parent::__construct($nodeId);

        $this->type = $type;
        $this->websiteId = $websiteId;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
