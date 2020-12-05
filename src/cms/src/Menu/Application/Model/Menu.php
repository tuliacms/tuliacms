<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Model;

use Tulia\Cms\Menu\Application\Query\Finder\Model\Menu as QueryModelMenu;

/**
 * @author Adam Banaszkiewicz
 */
class Menu
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $websiteId;

    /**
     * @var null|string
     */
    protected $name;

    public static function fromQueryModel(QueryModelMenu $menu): self
    {
        $self = new self($menu->getId(), $menu->getWebsiteId());
        $self->setName($menu->getName());

        return $self;
    }

    /**
     * @param string $id
     * @param string $websiteId
     */
    public function __construct(string $id, string $websiteId)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @param string $websiteId
     */
    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
