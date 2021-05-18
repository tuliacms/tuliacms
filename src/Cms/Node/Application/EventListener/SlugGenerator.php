<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Application\Event\NodeEvent;
use Tulia\Cms\Node\Application\Event\NodePreCreateEvent;
use Tulia\Cms\Node\Application\Event\NodePreUpdateEvent;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements EventSubscriberInterface
{
    protected SluggerInterface $slugger;

    protected NodeFinderInterface $nodeFinder;

    public function __construct(SluggerInterface $slugger, NodeFinderInterface $nodeFinder)
    {
        $this->slugger = $slugger;
        $this->nodeFinder = $nodeFinder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodePreCreateEvent::class => ['handle', 1000],
            NodePreUpdateEvent::class => ['handle', 1000],
        ];
    }

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
            ], ScopeEnum::INTERNAL);

            if ($node === null) {
                return $slugProposed;
            }
        }

        return $slug . '-' . time();
    }
}
