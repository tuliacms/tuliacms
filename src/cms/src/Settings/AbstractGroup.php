<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Options\OptionsInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractGroup implements GroupInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var OptionsInterface
     */
    protected $options;

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
        $this->options->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options->get($name, $default);
    }

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param OptionsInterface $options
     */
    public function setOptions(OptionsInterface $options): void
    {
        $this->options = $options;
    }
}
