<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Document;

/**
 * @author Adam Banaszkiewicz
 */
class Document implements DocumentInterface
{
    protected $title;
    protected $description;
    protected $keywords;
    protected $attributes = [];
    protected $links = [];
    protected $metas = [];

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(?string $name, ?string $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(?string $name, ?string $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function addLink(array $attributes): void
    {
        $this->links[] = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * {@inheritdoc}
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    /**
     * {@inheritdoc}
     */
    public function buildLinks(): string
    {
        $links = $this->links;

        $result = [];

        foreach ($links as $link) {
            $result[] = '<link '.$this->buildAttributes($link).' />';
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function addMeta(array $attributes): void
    {
        $this->metas[] = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetas(): array
    {
        return $this->metas;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetas(array $metas): void
    {
        $this->metas = $metas;
    }

    /**
     * {@inheritdoc}
     */
    public function buildMetas(): string
    {
        $metas = $this->metas;

        $metas[] = [
            'name'  => 'keywords',
            'content' => $this->keywords
        ];
        $metas[] = [
            'name'  => 'description',
            'content' => $this->description
        ];

        $result = [];

        foreach ($metas as $meta) {
            $result[] = '<meta '.$this->buildAttributes($meta).' />';
        }

        return implode(PHP_EOL, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function buildAttributes(array $attrs): string
    {
        $prepared = [];

        foreach ($attrs as $name => $val) {
            $prepared[] = $name.'="'.htmlspecialchars($val, ENT_QUOTES).'"';
        }

        return implode(' ', $prepared);
    }
}
