<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements NodeActionInterface
{
    protected SluggerInterface $slugger;

    protected NodeFinderInterface $nodeFinder;

    public function __construct(SluggerInterface $slugger, NodeFinderInterface $nodeFinder)
    {
        $this->slugger = $slugger;
        $this->nodeFinder = $nodeFinder;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public function execute(Node $node): void
    {
        $slug  = $node->getSlug();
        $title = $node->getTitle();

        if (! $slug && ! $title) {
            $node->setSlug(uniqid('temporary-slug-', true));
            return;
        }

        // Fallback to Node's title, if no slug provided.
        $input = $slug ?: $title;

        $slug = $this->findUniqueSlug($input, $node->getId()->getId());

        $node->setSlug($slug);
    }

    private function findUniqueSlug(string $slug, ?string $nodeId): string
    {
        $securityLoop  = 0;
        $slugGenerated = $this->slugger->url($slug);

        while ($securityLoop <= 100) {
            $slugProposed = $slugGenerated;

            if ($securityLoop > 0) {
                $slugProposed .= '-' . $securityLoop;
            }

            $securityLoop++;

            $node = $this->nodeFinder->findOne([
                'slug'       => $slugProposed,
                'id__not_in' => [$nodeId],
                'node_type'  => null,
                'order_by'   => null,
                'order_dir'  => null,
                'per_page'   => 1,
            ], NodeFinderScopeEnum::INTERNAL);

            if ($node === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
