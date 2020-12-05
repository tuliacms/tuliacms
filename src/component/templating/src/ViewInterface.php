<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

/**
 * @author Adam Banaszkiewicz
 */
interface ViewInterface
{
    /**
     * @return array
     */
    public function getViews(): array;

    /**
     * @param array $views
     */
    public function setViews(array $views): void;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @param array $data
     */
    public function addData(array $data): void;
}
