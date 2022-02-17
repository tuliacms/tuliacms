<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Model;

use ArrayIterator;
use IteratorAggregate;

/**
 * @author Adam Banaszkiewicz
 */
class Results implements IteratorAggregate
{
    protected array $hits  = [];

    protected array $label = [];

    protected string $icon  = '';

    public function __construct(array $hits = [])
    {
        $this->setHits($hits);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon'  => $this->icon,
            'hits'  => array_map(fn ($v) => $v->toArray(), $this->hits),
        ];
    }

    public function merge(Results $results): void
    {
        $this->appendHits($results->getHits());
    }

    public function add(string $name, Hit $hit): void
    {
        $this->hits[$name] = $hit;
    }

    public function getHits(): array
    {
        return $this->hits;
    }

    public function setHits(array $hits): void
    {
        foreach ($hits as $name => $hit) {
            $this->add($name, $hit);
        }
    }

    public function appendHits(array $hits): void
    {
        $this->hits = array_merge($this->hits, $hits);
    }

    public function getLabel(): array
    {
        return $this->label;
    }

    public function setLabel(array $label): void
    {
        $this->label = $label;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->hits);
    }
}
