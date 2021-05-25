<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\EditLinks;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\EditLinks\Domain\Collection;
use Tulia\Cms\EditLinks\Ports\Domain\EditLinksCollectorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model\Term;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksRegistrator implements EditLinksCollectorInterface
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

    public function collect(Collection $collection, object $term, array $options = []): void
    {
        try {
            $type = $this->registry->getType($term->getType());

            $collection->add('term.edit', [
                'link'  => $this->router->generate('backend.term.edit', [ 'taxonomyType' => $term->getType(), 'id' => $term->getId() ]),
                'label' => $this->translator->trans('editTerm', [
                    'term' => mb_strtolower($this->translator->trans('term', [], $type->getTranslationDomain())),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Term Type not exists.
        }
    }

    public function supports(object $object): bool
    {
        return $object instanceof Term;
    }
}
