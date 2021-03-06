<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Domain\Widgets;

use Tulia\Cms\Dashboard\Ports\Domain\Widgets\DashboardWidgetInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractDashboardWidget implements DashboardWidgetInterface
{
    private EngineInterface $engine;

    /**
     * {@inheritdoc}
     */
    abstract public function render(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function supports(string $group): bool;

    public function setTemplating(EngineInterface $engine): void
    {
        $this->engine = $engine;
    }

    public function view(string $view, array $data = []): string
    {
        return $this->engine->render(new View($view, $data));
    }
}
