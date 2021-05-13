<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Form;

use Symfony\Component\Form\AbstractType;
use Tulia\Component\FormSkeleton\Extension\ExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AbstractFormSkeletonType extends AbstractType implements FormSkeletonTypeInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions = [];

    public function setExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
