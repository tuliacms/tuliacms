<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
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
