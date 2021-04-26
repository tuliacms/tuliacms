<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Component\FormBuilder\ExtensionInterface;
use Tulia\Component\FormBuilder\Form;
use Tulia\Component\FormBuilder\FormPrototype;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @param FormFactoryInterface $formFactory
     * @param array $extensions
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        array $extensions
    ) {
        $this->formFactory = $formFactory;
        $this->extensions  = $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $options['form_extension_manager'] = $this;

        /** @var ExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            $extension->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(string $type = null, $data = null, array $options = []): FormInterface
    {
        $options['form_extension_manager'] = $this;

        $form = new FormPrototype($type, $data, $options);

        /** @var ExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            $extension->configureForm($form);
        }

        return $this->formFactory->create($form->getType(), $form->getData(), $form->getOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form): object
    {
        $data = $form->getData();

        /** @var ExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            $extension->handle($form, $data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(string $group = null): array
    {
        $sections = [];

        /** @var ExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            foreach ($extension->getSections() as $section) {
                if ($group === null) {
                    $sections[] = $section;
                } elseif ($section->getGroup() === $group) {
                    $sections[] = $section;
                }
            }
        }

        usort($sections, function ($a, $b) {
            return $b->getPriority() - $a->getPriority();
        });

        return $sections;
    }
}
