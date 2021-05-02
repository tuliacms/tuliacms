<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;

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
     * @param MenuId $menuId
     * @param string $locale
     */
    public function __construct(MenuId $menuId, string $locale)
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
