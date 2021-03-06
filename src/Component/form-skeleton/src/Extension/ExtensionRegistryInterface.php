<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Extension;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ExtensionRegistryInterface
{
    public function all(): iterable;

    public function add(ExtensionInterface $extension): void;

    /**
     * @param FormTypeInterface $formType
     * @param array $options
     * @param mixed $data
     * @return ExtensionInterface[]
     */
    public function getSupportive(FormTypeInterface $formType, array $options, $data = null): iterable;
}
