<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\WriteModel\OptionsRepositoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractGroup implements GroupInterface
{
    protected FormFactoryInterface $formFactory;

    protected OptionsRepositoryInterface $options;

    /**
     * {@inheritdoc}
     */
    abstract public function getId(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function getName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function buildView(): array;

    /**
     * {@inheritdoc}
     */
    abstract public function saveAction(array $data): bool;

    /**
     * {@inheritdoc}
     */
    abstract public function buildForm(): FormInterface;

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'fa fa-cogs';
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function view(string $view, array $data = [])
    {
        return [
            'view' => $view,
            'data' => $data,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(string $name, $value): void
    {
        $option = $this->options->find($name);
        $option->setValue($value);
        $this->options->update($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options->find($name)->getValue() ?? $default;
    }

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    public function setOptions(OptionsRepositoryInterface $options): void
    {
        $this->options = $options;
    }
}
