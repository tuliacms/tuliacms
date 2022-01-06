<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\LayoutTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutBuilder
{
    private LayoutTypeRegistry $layoutTypeRegistry;
    private LayoutTypeBuilderRegistry $builderRegistry;

    public function __construct(
        LayoutTypeRegistry $layoutTypeRegistry,
        LayoutTypeBuilderRegistry $builderRegistry
    ) {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->builderRegistry = $builderRegistry;
    }

    /**
     * @throws LayoutNotExists
     */
    public function build(ContentTypeFormDescriptor $formDescriptor): string
    {
        $type = $formDescriptor->getContentType();
        $layoutName = $type->getLayout();

        if ($this->layoutTypeRegistry->has($layoutName) === false) {
            throw LayoutNotExists::fromName($layoutName, $type->getCode());
        }

        $layout = $this->layoutTypeRegistry->get($layoutName);
        $builder = $this->builderRegistry->get($layout->getBuilder());

        return $builder->build(
            $type,
            $layout,
            $formDescriptor->getFormView()
        );
    }
}
