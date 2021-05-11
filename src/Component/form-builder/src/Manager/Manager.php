<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Component\FormBuilder\ExtensionInterface;
use Tulia\Component\FormBuilder\RegistryInterface;
use Tulia\Component\FormBuilder\Form\FormSkeletonTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Manager implements ManagerInterface
{
    protected FormFactoryInterface $formFactory;
    private RegistryInterface $registry;

    public function __construct(
        FormFactoryInterface $formFactory,
        RegistryInterface $registry
    ) {
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
