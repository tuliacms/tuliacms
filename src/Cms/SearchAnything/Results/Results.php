<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Results;

/**
 * @author Adam Banaszkiewicz
 */
class Results implements ResultsInterface
{
    /**
     * @var array
     */
    protected $hits  = [];

    /**
     * @var array
     */
    protected $label = [];

    /**
     * @var string
     */
    protected $icon  = '';

    /**
     * @param array $hits
     */
    public function __construct(array $hits = [])
    {
        $this->setHits($hits);
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon'  => $this->icon,
            'hits'  => array_map(function ($v) {return $v->toArray();}, $this->hits),
        ];
    }

    /**
     * @param ResultsInterface $results
     */
    public function merge(ResultsInterface $results): void
    {
        $this->appendHits($results->getHits());
    }

    /**
     * @param Hit $hit
     */
    public function add(Hit $hit): void
    {
        $this->hits[] = $hit;
    }

    /**
     * @return array
     */
    public function getHits(): array
    {
        return $this->hits;
    }

    /**
     * @param array $hits
     */
    public function setHits(array $hits): void
    {
        foreach ($hits as $hit) {
            $this->add($hit);
        }
    }

    /**
     * @param array $hits
     */
    public function appendHits(array $hits): void
    {
        $this->hits = array_merge($this->hits, $hits);
    }

    /**
     * @return array
     */
    public function getLabel(): array
    {
        return $this->label;
    }

    /**
     * @param array $label
     */
    public function setLabel(array $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }
}
