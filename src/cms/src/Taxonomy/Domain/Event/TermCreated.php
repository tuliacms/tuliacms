<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Event;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class TermCreated extends DomainEvent
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
     * @param AggregateId $termId
     * @param string $type
     * @param string $websiteId
     * @param string $locale
     */
    public function __construct(AggregateId $termId, string $type, string $websiteId, string $locale)
    {
        parent::__construct($termId);

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
