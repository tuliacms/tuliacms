<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

/**
 * @author Adam Banaszkiewicz
 */
interface EngineInterface
{
    /**
     * @param ViewInterface $view
     *
     * @return string|null
     */
    public function render(ViewInterface $view): ?string;

    /**
     * @param string $view
     * @param array $data
     * @param string|null $debugName
     *
     * @return string|null
     */
    public function renderString(string $view, array $data = [], string $debugName = null): ?string;
}
