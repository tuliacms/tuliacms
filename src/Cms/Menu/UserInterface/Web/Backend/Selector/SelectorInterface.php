<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Backend\Selector;

use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SelectorInterface
{
    public function render(TypeInterface $type, string $identity): string;
}
