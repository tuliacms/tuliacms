<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Node\Application\Event\NodeEvent;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Shared\Slug\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator
{
    /**
     * @var SluggerInterface
     */
    protected $slugger;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger, FinderFactoryInterface $finderFactory)
    {
        $this->slugger       = $slugger;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param NodeEvent $event
     *
     * @throws MultipleFetchException
     * @throws QueryException
     */
    public function handle(NodeEvent $event): void
    {
        /** @var Node $node */
        $node  = $event->getNode();
        /** @var string $slug */
        $slug  = $node->getSlug();
        /** @var string $title */
        $title = $node->getTitle();

        if (! $slug && ! $title) {
            $node->setSlug(uniqid('temporary-slug-', true));
            return;
        }

        // Fallback to Node's title, if no slug provided.
        $input = $slug ?: $title;

        $slug = $this->findUniqueSlug($input, $node->getId());

        $node->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param string|null $nodeId
     *
     * @return string
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
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

            $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
            $finder->setCriteria([
                'slug'       => $slugProposed,
                'id__not_in' => [$nodeId],
                'node_type'  => null,
                'order_by'   => null,
                'order_dir'  => null,
                'per_page'   => 1,
            ]);
            $finder->fetchRaw();
            $node = $finder->getResult()->first();

            if ($node === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
