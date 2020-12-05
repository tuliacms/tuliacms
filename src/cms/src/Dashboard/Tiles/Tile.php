<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
class Tile implements TileInterface
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @param string $name
     * @param string $link
     * @param string $icon
     * @param string $description
     */
    public function __construct(string $name, string $link, string $icon, string $description = '')
    {
        $this->name = $name;
        $this->link = $link;
        $this->icon = $icon;
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
