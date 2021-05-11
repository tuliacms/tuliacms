<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    public function getSections(FormSkeletonTypeInterface $form, string $group = null): array;
}
