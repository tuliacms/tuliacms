<?php

declare(strict_types = 1);

namespace Tulia\Cms\Widget\Domain\Catalog;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetInterface
{
    public function configure(ConfigurationInterface $config): void;

    /**
     * Method called on front, when widget is rendered with declared config.
     */
    public function render(ConfigurationInterface $config): ?ViewInterface;

    /**
     * Method called on backend, to provide view and view data for widget create/edit view.
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface;

    /**
     * Method called on backend, when saving widget create/edit form.
     */
    public function saveAction(FormInterface $form, ConfigurationInterface $config): void;

    public function view(string $view, array $data = []): ViewInterface;
}
