<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class TitleChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $title;

    /**
     * @param AggregateId $widgetId
     * @param null|string $title
     */
    public function __construct(AggregateId $widgetId, ?string $title)
    {
        parent::__construct($widgetId);

        $this->title = $title;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
