<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Metadata\Domain\WriteModel\Model\Attribute;

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

        $attributes = [];
        $decodedData = [];

        foreach ($data as $uri => $value) {
            if ($value instanceof Attribute) {
                $attributes[$uri] = $value;
            } else {
                $decodedData[$uri] = $value;
            }
        }

        $attributes = $this->decodeAttributes($attributes);

        $data = array_merge($attributes, $decodedData);

        $form = $this->formBuilder->createForm($contentType, $data);
        $form->handleRequest($request);

        return new ContentTypeFormDescriptor(
            $contentType,
            $form
        );
    }

    /**
     * @param Attribute[] $attributes
     */
    private function decodeAttributes(array $attributes): array
    {
        $output = [];

        foreach ($attributes as $attribute) {
            parse_str($attribute->getUri().'=v', $result);

            $value = $this->assignValueToMostDeepIndex($result, $attribute->getValue());
            $output = $this->mergeRecursive($output, $value);
        }

        return $output;
    }

    private function assignValueToMostDeepIndex(&$input, $value)
    {
        if (is_array($input)) {
            foreach ($input as &$item) {
                $this->assignValueToMostDeepIndex($item, $value);
            }

            unset($item);
        }

        if ($input === 'v') {
            $input = $value;
        }

        return $input;
    }

    private function mergeRecursive(&$target, $value)
    {
        foreach ($value as $key => &$item) {
            if (is_array($item)) {
                if (isset($target[$key]) === false) {
                    $target[$key] = [];
                }

                $target[$key] = $this->mergeRecursive($target[$key], $item);
            } else {
                $target[$key] = $item;
            }
        }

        return $target;
    }
}
