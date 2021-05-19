<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements TermActionInterface
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

            $term = $this->termFinder->findOne([
                'slug'       => $slugProposed,
                'id__not_in' => [$termId],
                'ntaxonomy_type' => null,
                'order_by'   => null,
                'order_dir'  => null,
                'per_page'   => 1,
            ], TermFinderScopeEnum::INTERNAL);

            if ($term === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
