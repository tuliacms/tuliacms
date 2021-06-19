<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget;

use Symfony\Component\Form\FormInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetInterface
{
    public static function getId(): string;

    public function configure(ConfigurationInterface $config): void;

    public function getInfo(): array;

    public function getViewsDirectory(): string;

    /**
     * Method called on front, when widget is rendered with declared config.
     */
    public function render(ConfigurationInterface $config): ?ViewInterface;

    /**
     * Method called on backend, to build and provide configuration form for widget.
     */
    public function getForm(ConfigurationInterface $config): ?string;

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
