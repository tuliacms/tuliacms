<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    public function getSections(FormSkeletonTypeInterface $form, string $group = null): array;
}
