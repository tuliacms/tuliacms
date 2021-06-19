<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget;

use Symfony\Component\Form\FormInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWidget implements WidgetInterface
{
    protected string $viewsDirectory;

    /**
     * {@inheritdoc}
     */
    public function configure(ConfigurationInterface $config): void
    {}

    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ConfigurationInterface $config): ?string
    {
        return null;
    }

    public function getViewsDirectory(): string
    {
        if ($this->viewsDirectory) {
            return $this->viewsDirectory;
        }

        $reflection = new \ReflectionClass($this);

        return $this->viewsDirectory = \dirname($reflection->getFileName());
    }

    public function saveAction(FormInterface $form, ConfigurationInterface $config): void
    {

    }

    public function view(string $view, array $data = []): ViewInterface
    {
        return new View($view, $data);
    }
}
