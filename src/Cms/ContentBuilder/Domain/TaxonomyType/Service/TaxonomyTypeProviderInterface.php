<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyTypeProviderInterface
{
    /**
     * @return TaxonomyType[]
     */
    public function provide(): array;
}
