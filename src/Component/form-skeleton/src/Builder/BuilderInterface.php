<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Builder;

use Symfony\Component\Form\FormView;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function build(FormView $manager, ?string $group = null, array $options = []): string;
}
