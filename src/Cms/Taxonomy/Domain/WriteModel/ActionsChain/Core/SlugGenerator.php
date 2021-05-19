<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements TermActionInterface
{
    protected SluggerInterface $slugger;

    protected FinderFactoryInterface $finderFactory;

    public function __construct(SluggerInterface $slugger, FinderFactoryInterface $finderFactory)
    {
        $this->slugger = $slugger;
        $this->finderFactory = $finderFactory;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public function execute(Term $term): void
    {
        $slug  = $term->getSlug();
        $name = $term->getName();

        if (! $slug && ! $name) {
            $term->setSlug(uniqid('temporary-slug-', true));
            return;
        }

        // Fallback to Term's name, if no slug provided.
        $input = $slug ?: $name;

        $slug = $this->findUniqueSlug($input, $term->getId()->getId());

        $term->setSlug($slug);
    }

    private function findUniqueSlug(string $slug, ?string $termId): string
    {
        $securityLoop  = 0;
        $slugGenerated = $this->slugger->url($slug);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $finder = $this->finderFactory->getInstance(\Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum::INTERNAL);
            $finder->setCriteria([
                'slug'       => $slugProposed,
                'id__not_in' => [$termId],
                'ntaxonomy_type' => null,
                'order_by'   => null,
                'order_dir'  => null,
                'per_page'   => 1,
            ]);
            $finder->fetchRaw();
            $term = $finder->getResult()->first();

            if ($term === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
