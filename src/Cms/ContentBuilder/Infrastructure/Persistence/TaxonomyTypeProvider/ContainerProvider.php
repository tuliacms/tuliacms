<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\TaxonomyTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\AbstractTaxonomyTypeProvider;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider extends AbstractTaxonomyTypeProvider
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function provide(): array
    {
        $types = [];

        foreach ($this->config as $name => $options) {
            $types[] = $this->buildTaxonomyType($name, $options, true);
        }

        return $types;
    }
}
