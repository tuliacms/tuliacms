<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

use Tulia\Component\FormBuilder\Form\FormSkeletonTypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerInterface
{
    public function getSections(FormSkeletonTypeInterface $form, string $group = null): array;
}
