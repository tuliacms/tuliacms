<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    /**
     * @param ManagerInterface $manager
     * @param string|null $group
     * @param array $options
     *
     * @return string
     */
    public function build(ManagerInterface $manager, ?string $group = null, array $options = []): string;
}
