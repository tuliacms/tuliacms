<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Section;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionsFactoryInterface
{
    public function create(string $id, array $params = []): SectionInterface;
}
