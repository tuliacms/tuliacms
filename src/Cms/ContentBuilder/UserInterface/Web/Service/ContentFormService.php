<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Attributes\Domain\WriteModel\Service\UriToArrayTransformer;

/**
 * @author Adam Banaszkiewicz
 */
class ContentFormService
{
    private ContentTypeRegistry $contentTypeRegistry;
    private SymfonyFormBuilder $formBuilder;
    private UriToArrayTransformer $attributesToArrayTransformer;

    public function __construct(
        ContentTypeRegistry $contentTypeRegistry,
        SymfonyFormBuilder $formBuilder,
        UriToArrayTransformer $attributesToArrayTransformer
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
        $this->attributesToArrayTransformer = $attributesToArrayTransformer;
    }

    public function buildFormDescriptor(string $type, array $data, Request $request): ContentTypeFormDescriptor
    {
        $contentType = $this->contentTypeRegistry->get($type);

        $attributes = [];
        $decodedData = [];

        foreach ($data as $uri => $value) {
            if ($value instanceof Attribute) {
                $attributes[$uri] = $value;
            } else {
                $decodedData[$uri] = $value;
            }
        }

        $attributes = $this->attributesToArrayTransformer->transform($attributes);

        $data = array_merge($attributes, $decodedData);

        $form = $this->formBuilder->createForm($contentType, $data);
        $form->handleRequest($request);

        return new ContentTypeFormDescriptor(
            $contentType,
            $form
        );
    }
}
