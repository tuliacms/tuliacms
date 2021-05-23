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
        return ['save' => 100];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        $strategy = $this->strategyRegistry->get(
            $this->registry->getType($taxonomy->getType()->getType())->getRoutingStrategy()
        );

        foreach ($this->getTerms($taxonomy) as $term) {
            $this->createPathForTerm($taxonomy, $term, $strategy);
        }
    }

    private function createPathForTerm(Taxonomy $taxonomy, Term $term, TaxonomyRoutingStrategyInterface $strategy): void
    {
        if ($term->isRoot()) {
            return;
        }

        $path = $strategy->generateFromTaxonomy(
            $taxonomy,
            $term->getId()->getId()
        );

        // Remove path for non visible terms
        if ($this->isTermVisible($taxonomy, $term) === false) {
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

    private function isTermVisible(Taxonomy $taxonomy, Term $term): bool
    {
        $visible = true;

        do {
            if ($term->isVisible() === false) {
                $visible = false;
            }

            if ($term->getParentId()) {
                $term = $taxonomy->getTerm($term->getParentId());
            } else {
                $term = null;
            }
        } while ($term);

        return $visible;
    }
}
