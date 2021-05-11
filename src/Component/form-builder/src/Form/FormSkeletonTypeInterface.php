<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Form;

use Tulia\Component\FormBuilder\ExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface FormSkeletonTypeInterface
{
    /**
     * @param ExtensionInterface[] $extensions
     */
    public function setExtensions(array $extensions): void;

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array;
}
