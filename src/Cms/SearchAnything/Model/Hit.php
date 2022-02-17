<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Hit
{
    protected string $title;

    protected string $link;

    protected ?string $description = null;

    protected ?string $image = null;

    protected array $tags = [];

    public function __construct(string $title, string $link)
    {
        $this->title = $title;
        $this->link = $link;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'link'  => $this->link,
            'tags'  => $this->tags,
        ];
    }

    public function addTag(string $tag, string $icon = null): void
    {
        $this->tags[] = [
            'tag'  => $tag,
            'icon' => $icon,
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
