<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyRegistry;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\Routing\Strategy\TaxonomyRoutingStrategyInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PathGenerator implements TaxonomyActionInterface
{
    private TaxonomyRoutingStrategyRegistry $strategyRegistry;

    private RegistryInterface $registry;

    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        TaxonomyRoutingStrategyRegistry $strategyRegistry,
        RegistryInterface $registry,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->strategyRegistry = $strategyRegistry;
        $this->registry = $registry;
        $this->currentWebsite = $currentWebsite;
    }

    public static function supports(): array
    {
        return [
            'save' => 20,
        ];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        $strategy = $this->strategyRegistry->get(
            $this->registry->getType($taxonomy->getType()->getType())->getRoutingStrategy()
        );

        foreach ($this->getTerms($taxonomy) as $term) {
            $this->createPathForTerm($term, $strategy);
        }
    }

    private function createPathForTerm(Term $term, TaxonomyRoutingStrategyInterface $strategy): void
    {
        $path = $strategy->generate(
            $term->getId()->getId(),
            $term->getLocale(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        // Remove path for non visible terms
        if ($term->isVisible() === false) {
            $path = null;
        }

        if ($term->getPath() === $path) {
            return;
        }

        $term->setPath($path);
    }

    /**
     * @return Term[]
     */
    private function getTerms(Taxonomy $taxonomy): array
    {
        return array_merge(...array_values($taxonomy->collectChangedTerms()));
    }
}
