<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Widgets;

use Tulia\Component\Templating\View;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * {@inheritdoc}
     */
    abstract public function render(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function supports(string $group): bool;

    /**
     * @param EngineInterface $engine
     */
    public function setTemplating(EngineInterface $engine): void
    {
        $this->engine = $engine;
    }

    /**
     * @param string $view
     * @param array $data
     *
     * @return string
     */
    public function view(string $view, array $data = []): string
    {
        return $this->engine->render(new View($view, $data));
    }
}
