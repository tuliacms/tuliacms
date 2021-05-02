<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\WriteModel\Event;

use Tulia\Cms\Menu\Domain\WriteModel\Model\ValueObject\MenuId;

/**
 * @author Adam Banaszkiewicz
 */
class Renamed extends DomainEvent
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @param MenuId $itemId
     * @param null|string $name
     */
    public function __construct(MenuId $itemId, ?string $name)
    {
        parent::__construct($itemId);

        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
