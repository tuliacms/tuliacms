<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function build(string $id, string $type, array $params): ?string;
}
