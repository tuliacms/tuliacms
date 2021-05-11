<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Component\FormBuilder\Extension\ExtensionInterface;
use Tulia\Component\FormBuilder\Extension\ExtensionRegistryInterface;

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
        $sections = [];

        /** @var ExtensionInterface $extension */
        foreach ($formSkeleton->getExtensions() as $extension) {
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
