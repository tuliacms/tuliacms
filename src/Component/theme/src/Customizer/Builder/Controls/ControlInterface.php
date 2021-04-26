<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

/**
 * @author Adam Banaszkiewicz
 */
interface ControlInterface
{
    public function build(array $params): string;
    public static function getName(): string;
}
