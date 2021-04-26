<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Menu\Event;

use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class MenuCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param AggregateId $menuId
     * @param string $locale
     */
    public function __construct(AggregateId $menuId, string $locale)
    {
        parent::__construct($menuId);

        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
