<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Form\SymfonyForm;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Tulia\Component\FormSkeleton\Form\FormSkeletonTypeInterface;
use Tulia\Component\FormSkeleton\Extension\ExtensionRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormFactory implements FormFactoryInterface
{
    private FormFactoryInterface $symfonyFormFactory;
    private FormRegistryInterface $typeRegistry;
    private ExtensionRegistryInterface $extensionRegistry;

    public function __construct(
        FormFactoryInterface $symfonyFormFactory,
        FormRegistryInterface $typeRegistry,
        ExtensionRegistryInterface $extensionRegistry
    ) {
        $this->symfonyFormFactory = $symfonyFormFactory;
        $this->typeRegistry = $typeRegistry;
        $this->extensionRegistry = $extensionRegistry;
    }

    public function create(
        string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType',
        $data = null,
        array $options = []
    ) {
        return $this->createBuilder($type, $data, $options)->getForm();
    }

    public function createNamed(
        string $name,
        string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType',
        $data = null,
        array $options = []
    ) {
        return $this->createNamedBuilder($name, $type, $data, $options)->getForm();
    }

    public function createForProperty(string $class, string $property, $data = null, array $options = [])
    {
        return $this->createBuilderForProperty($class, $property, $data, $options)->getForm();
    }

    public function createBuilder(
        string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType',
        $data = null,
        array $options = []
    ) {
        return $this->createNamedBuilder($this->typeRegistry->getType($type)->getBlockPrefix(), $type, $data, $options);
    }

    public function createNamedBuilder(
        string $name,
        string $type = 'Symfony\Component\Form\Extension\Core\Type\FormType',
        $data = null,
        array $options = []
    ) {
        $formType = $this->typeRegistry->getType($type)->getInnerType();
        $options['form_type_instance'] = $formType;

        $builder = $this->symfonyFormFactory->createNamedBuilder($name, $type, $data, $options);

        if ($formType instanceof FormSkeletonTypeInterface) {
            $extensions = $this->extensionRegistry->getSupportive(
                $formType,
                $options,
                $data
            );

            $formType->setExtensions($extensions);

            foreach ($extensions as $extension) {
                $extension->buildForm($builder, $options);
            }
        }

        return $builder;
    }

    public function createBuilderForProperty(string $class, string $property, $data = null, array $options = [])
    {
        return $this->symfonyFormFactory->createBuilderForProperty($class, $property, $data, $options);
    }
}
