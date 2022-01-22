<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutBuilder
{
    private LayoutTypeBuilderRegistry $builderRegistry;

    public function __construct(LayoutTypeBuilderRegistry $builderRegistry)
    {
        $this->builderRegistry = $builderRegistry;
    }

    /**
     * @throws LayoutNotExists
     */
    public function build(ContentTypeFormDescriptor $formDescriptor): string
    {
        $type = $formDescriptor->getContentType();

        return $this->builderRegistry
            ->get($type->getLayout()->getBuilder())
            ->build($type, $formDescriptor->getFormView());
    }
}
