<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractWidget implements WidgetInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(ConfigurationInterface $config): void
    {}

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

    public function saveAction(FormInterface $form, ConfigurationInterface $config): void
    {

    }

    public function view(string $view, array $data = []): ViewInterface
    {
        return new View($view, $data);
    }
}
