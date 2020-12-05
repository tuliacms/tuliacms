<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinks
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, RegistryInterface $registry)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    /**
     * @param CollectEditLinksEvent $event
     *
     * @throws RouteNotFoundException
     */
    public function handle(CollectEditLinksEvent $event): void
    {
        /** @var Term $term */
        $term = $event->getObject();

        if (!$term instanceof Term) {
            return;
        }

        try {
            $type = $this->registry->getType($term->getType());

            $event->add('term.edit', [
                'link'  => $this->router->generate('backend.term.edit', [ 'taxonomy_type' => $term->getType(), 'id' => $term->getId() ]),
                'label' => $this->translator->trans('editTerm', [
                    'term' => mb_strtolower($this->translator->trans('term', [], $type->getTranslationDomain())),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Term Type not exists.
        }
    }
}
