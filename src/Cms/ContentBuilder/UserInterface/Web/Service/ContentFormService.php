<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
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

    public function buildFormDescriptor(string $type, array $data, Request $request): ContentTypeFormDescriptor
    {
        $contentType = $this->contentTypeRegistry->get($type);

        $form = $this->formBuilder->createForm($contentType, $data);
        $form->handleRequest($request);

        return new ContentTypeFormDescriptor(
            $contentType,
            $form
        );
    }
}
