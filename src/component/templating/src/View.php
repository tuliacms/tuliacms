<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

/**
 * @author Adam Banaszkiewicz
 */
class View implements ViewInterface
{
    protected $views = [];
    protected $data  = [];

    /**
     * @param       $views
     * @param array $data
     */
    public function __construct($views, array $data = [])
    {
        if (\is_array($views) === false) {
            $views = [ $views ];
        }

        $this->views = $views;
        $this->data  = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * {@inheritdoc}
     */
    public function setViews(array $views): void
    {
        $this->views = $views;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
}
