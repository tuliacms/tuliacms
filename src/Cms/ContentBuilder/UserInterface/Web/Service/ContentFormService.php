<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFormService
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private SymfonyFormBuilderCreator $formBuilder;
    private UriToArrayTransformer $attributesToArrayTransformer;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        SymfonyFormBuilderCreator $formBuilder,
        UriToArrayTransformer $attributesToArrayTransformer
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
        $this->attributesToArrayTransformer = $attributesToArrayTransformer;
    }

    public function buildFormDescriptor(string $type, array $data, array $viewContext = []): ContentTypeFormDescriptor
    {
        $contentType = $this->contentTypeRegistry->get($type);

        if (isset($data['attributes']) && \is_array($data['attributes'])) {
            $attributes = $this->attributesToArrayTransformer->transform($data['attributes']);
            unset($data['attributes']);
            $flattened = array_merge($attributes, $data);
        } else {
            $flattened = $data;
        }

        $form = $this->formBuilder->createBuilder($contentType, $flattened);

        return new ContentTypeFormDescriptor($contentType, $form, $viewContext);
    }
}
