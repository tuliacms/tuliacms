<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets;

/**
 * @author Adam Banaszkiewicz
 */
interface DashboardWidgetInterface
{
    public function render(): string;

    public function supports(string $group): bool;
}
