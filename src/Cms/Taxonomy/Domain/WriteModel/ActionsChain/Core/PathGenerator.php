<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Router;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class PathGenerator implements TaxonomyActionInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public static function supports(): array
    {
        return ['save' => 100];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        foreach ($this->getTerms($taxonomy) as $term) {
            $this->createPathForTerm($taxonomy, $term);
        }
    }

    private function createPathForTerm(Taxonomy $taxonomy, Term $term): void
    {
        $path = $this->router->generate(
            $taxonomy->getType(),
            $term->getId()->getValue(),
            ['_locale' => $term->getLocale(), '_term_instance' => $term]
        );

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
        $terms = [];

        foreach ($taxonomy->terms() as $term) {
            if ($term->isRoot()) {
                continue;
            }

            // Remove path for non visible terms
            if ($this->isTermVisible($taxonomy, $term) === false) {
                continue;
            }

            $terms[] = $term;
        }

        return $terms;
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
