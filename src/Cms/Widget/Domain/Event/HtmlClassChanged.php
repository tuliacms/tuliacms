<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\Event;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class HtmlClassChanged extends DomainEvent
{
    /**
     * @var null|string
     */
    private $htmlClass;

    /**
     * @param AggregateId $widgetId
     * @param null|string $htmlClass
     */
    public function __construct(AggregateId $widgetId, ?string $htmlClass)
    {
        parent::__construct($widgetId);

        $this->htmlClass = $htmlClass;
    }

    /**
     * @return null|string
     */
    public function getHtmlClass(): ?string
    {
        return $this->htmlClass;
    }
}
