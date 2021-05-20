<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TaxonomyActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements TaxonomyActionInterface
{
    private SluggerInterface $slugger;

    private TermFinderInterface $termFinder;

    public function __construct(SluggerInterface $slugger, TermFinderInterface $termFinder)
    {
        $this->slugger = $slugger;
        $this->termFinder = $termFinder;
    }

    public static function supports(): array
    {
        return ['save' => 900];
    }

    public function execute(Taxonomy $taxonomy): void
    {
        foreach ($this->getTerms($taxonomy) as $term) {
            $this->createSlugForTerm($term);
        }
    }

    /**
     * @return Term[]
     */
    private function getTerms(Taxonomy $taxonomy): array
    {
        return array_merge(...array_values($taxonomy->collectChangedTerms()));
    }

    private function createSlugForTerm(Term $term): void
    {
        $slug = $term->getSlug();
        $name = $term->getName();

        if (! $slug && ! $name) {
            $term->setSlug(uniqid('temporary-slug-', true));
            return;
        }

        // Fallback to Term's name, if no slug provided.
        $input = $slug ?: $name;
        $slug = $this->findUniqueSlug($input, $term->getId()->getId());

        // Do not update when already the same.
        if ($slug === $term->getSlug()) {
            return;
        }

        $term->setSlug($slug);
    }

    private function findUniqueSlug(string $slug, ?string $termId): string
    {
        $securityLoop = 0;
        $slugGenerated = $this->slugger->url($slug);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $term = $this->termFinder->findOne([
                'slug'       => $slugProposed,
                'id__not_in' => [$termId],
                'taxonomy_type' => null,
                'order_by'   => null,
                'order_dir'  => null,
            ], TermFinderScopeEnum::INTERNAL);

            if ($term === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
