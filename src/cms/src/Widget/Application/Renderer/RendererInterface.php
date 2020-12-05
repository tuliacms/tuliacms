<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Application\Renderer;

/**
 * @author Adam Banaszkiewicz
 */
interface RendererInterface
{
    /**
     * @param string $id
     *
     * @return string
     */
    public function forId(string $id): string;

    /**
     * @param string $space
     *
     * @return string
     */
    public function forSpace(string $space): string;
}
