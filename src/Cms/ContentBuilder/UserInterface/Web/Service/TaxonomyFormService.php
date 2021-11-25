<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyFormService
{
    private TaxonomyTypeRegistry $taxonomyTypeRegistry;
    private SymfonyFormBuilder $formBuilder;

    public function __construct(
        TaxonomyTypeRegistry $taxonomyTypeRegistry,
        SymfonyFormBuilder $formBuilder
    ) {
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
        $this->formBuilder = $formBuilder;
    }

    public function buildFormDescriptor(string $type, array $nodeData, Request $request): ContentTypeFormDescriptor
    {
        $taxonomyType = $this->taxonomyTypeRegistry->get($type);

        $form = $this->formBuilder->createForm($taxonomyType, $nodeData);
        $form->handleRequest($request);

        return new ContentTypeFormDescriptor(
            $taxonomyType,
            $form
        );
    }
}
