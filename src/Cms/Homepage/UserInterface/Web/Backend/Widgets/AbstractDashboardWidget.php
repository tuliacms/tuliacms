<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets;

use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

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
