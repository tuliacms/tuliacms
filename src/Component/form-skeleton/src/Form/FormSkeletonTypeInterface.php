<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Form;

use Tulia\Component\FormSkeleton\Extension\ExtensionInterface;

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
