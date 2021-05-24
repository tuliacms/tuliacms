<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Ports\Domain\Widgets;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardWidgetInterface
{
    public function render(): string;

    public function supports(string $group): bool;
}
