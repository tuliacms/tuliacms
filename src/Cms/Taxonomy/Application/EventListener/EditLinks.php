<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinks implements EventSubscriberInterface
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;
    protected RegistryInterface $registry;

    public function __construct(TranslatorInterface $translator, RouterInterface $router, RegistryInterface $registry)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CollectEditLinksEvent::class => ['handle', 0],
        ];
    }

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
