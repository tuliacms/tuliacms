<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Taxonomy\Application\Event\TermEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreCreateEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermPreUpdateEvent;
use Tulia\Cms\Taxonomy\Query\Exception\MultipleFetchException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SlugGenerator implements EventSubscriberInterface
{
    protected SluggerInterface $slugger;
    protected FinderFactoryInterface $finderFactory;

    public function __construct(SluggerInterface $slugger, FinderFactoryInterface $finderFactory)
    {
        $this->slugger       = $slugger;
        $this->finderFactory = $finderFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TermPreCreateEvent::class => ['handle', 1000],
            TermPreUpdateEvent::class => ['handle', 1000],
        ];
    }

    /**
     * @param TermEvent $event
     *
     * @throws MultipleFetchException
     * @throws QueryException
     */
    public function handle(TermEvent $event): void
    {
        /** @var Term $term */
        $term  = $event->getTerm();
        /** @var string $slug */
        $slug  = $term->getSlug();
        /** @var string $name */
        $name = $term->getName();

        if (! $slug && ! $name) {
            $term->setSlug(uniqid('temporary-slug-', true));
            return;
        }

        // Fallback to Term's name, if no slug provided.
        $input = $slug ?: $name;

        $slug = $this->findUniqueSlug($input, $term->getId());

        $term->setSlug($slug);
    }

    /**
     * @param string $slug
     * @param string|null $termId
     *
     * @return string
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
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

            $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
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
