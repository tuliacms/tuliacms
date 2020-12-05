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
    /**
     * @param ConfigurationInterface $config
     *
     * @return void
     */
    public function configure(ConfigurationInterface $config): void;

    /**
     * @return array
     */
    public function getInfo(): array;

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getViewsDirectory(): string;

    /**
     * Method called on front, when widget is rendered with declared config.
     *
     * @param ConfigurationInterface $config
     *
     * @return ViewInterface|null
     */
    public function render(ConfigurationInterface $config): ?ViewInterface;

    /**
     * Method called on backend, to build and provide configuration form for widget.
     *
     * @param ConfigurationInterface $config
     *
     * @return FormInterface|null
     */
    public function getForm(ConfigurationInterface $config): ?string;

    /**
     * Method called on backend, to provide view and view data for widget create/edit view.
     *
     * @param ConfigurationInterface $config
     *
     * @return ViewInterface
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface;

    /**
     * Method called on backend, when saving widget create/edit form.
     *
     * @param FormInterface $form
     * @param ConfigurationInterface $config
     */
    public function saveAction(FormInterface $form, ConfigurationInterface $config): void;

    public function view(string $view, array $data = []): ViewInterface;
}
