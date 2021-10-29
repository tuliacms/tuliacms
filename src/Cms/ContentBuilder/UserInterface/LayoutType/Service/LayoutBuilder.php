<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\FormDescriptor;

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
    public function build(FormDescriptor $formDescriptor): string
    {
        $type = $formDescriptor->getNodeType();
        $layoutName = $type->getLayout();

        if ($this->layoutTypeRegistry->has($layoutName) === false) {
            throw LayoutNotExists::fromName($layoutName, $type->getType());
        }

        $layout = $this->layoutTypeRegistry->get($layoutName);
        $builder = $this->builderRegistry->get($layoutName);

        return $builder->build(
            $type,
            $layout,
            $formDescriptor->getFormView()
        );
    }
}
