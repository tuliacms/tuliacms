<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Component\FormBuilder\Extension\ExtensionInterface;
use Tulia\Component\FormBuilder\Extension\ExtensionRegistryInterface;
use Tulia\Component\FormBuilder\Section\SectionsBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    protected FormFactoryInterface $formFactory;
    protected ExtensionRegistryInterface $registry;

    public function __construct(FormFactoryInterface $formFactory, ExtensionRegistryInterface $registry)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(FormSkeletonTypeInterface $formSkeleton, string $group = null): array
    {
        $sections = new SectionsBuilder();

        /** @var ExtensionInterface $extension */
        foreach ($formSkeleton->getExtensions() as $extension) {
            $extension->getSections($sections);
        }

        return $sections->all($group);
    }
}
