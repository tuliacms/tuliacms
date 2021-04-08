<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
class Identity implements IdentityInterface
{
    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @var array
     */
    protected $cacheTags = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(string $link, array $cacheTags = [])
    {
        $this->setLink($link);
        $this->setCacheTags($cacheTags);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
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
    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheTags(array $cacheTags): void
    {
        $this->cacheTags = $cacheTags;
    }
}
