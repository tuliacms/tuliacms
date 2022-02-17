<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\EditLinks;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\EditLinks\Model\Collection;
use Tulia\Cms\EditLinks\Service\EditLinksCollectorInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksRegistrator implements EditLinksCollectorInterface
{
    protected TranslatorInterface $translator;

    protected RouterInterface $router;

    protected ContentTypeRegistryInterface $registry;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        ContentTypeRegistryInterface $registry
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    public function collect(Collection $collection, object $term, array $options = []): void
    {
        try {
            $type = $this->registry->get($term->getType());

            $collection->add('term.edit', [
                'link'  => $this->router->generate('backend.term.edit', [ 'taxonomyType' => $term->getType(), 'id' => $term->getId() ]),
                'label' => $this->translator->trans('editTerm', [
                    'term' => mb_strtolower($this->translator->trans($type->getName(), [], 'taxonomy')),
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
