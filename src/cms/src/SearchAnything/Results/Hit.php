<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Results;

/**
 * @author Adam Banaszkiewicz
 */
class Hit
{
    protected $id;
    protected $title;
    protected $description;
    protected $image;
    protected $link;
    protected $tags = [];

    /**
     * @param string $title
     * @param string $link
     */
    public function __construct(string $title, string $link)
    {
        $this->title = $title;
        $this->link  = $link;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'link'  => $this->link,
            'tags'  => $this->tags,
        ];
    }

    /**
     * @param string $tag
     * @param string|null $icon
     */
    public function addTag(string $tag, string $icon = null): void
    {
        $this->tags[] = [
            'tag'  => $tag,
            'icon' => $icon,
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
