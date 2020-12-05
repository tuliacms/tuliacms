<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Widgets;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetInterface
{
    public function render(): string;
    public function supports(string $group): bool;
}
