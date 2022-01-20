<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFormService
{
    private ContentTypeRegistry $contentTypeRegistry;
    private SymfonyFormBuilder $formBuilder;

    public function __construct(
        ContentTypeRegistry $contentTypeRegistry,
        SymfonyFormBuilder $formBuilder
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
    }

    public function buildFormDescriptor(string $type, array $nodeData, Request $request): ContentTypeFormDescriptor
    {
        $nodeType = $this->contentTypeRegistry->get($type);

        $form = $this->formBuilder->createForm($nodeType, $nodeData);
        $form->handleRequest($request);

        return new ContentTypeFormDescriptor(
            $nodeType,
            $form
        );
    }
}
