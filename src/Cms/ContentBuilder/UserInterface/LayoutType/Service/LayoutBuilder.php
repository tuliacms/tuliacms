<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
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

    public function build(FormDescriptor $formDescriptor): string
    {
        $layout = $this->layoutTypeRegistry->get($formDescriptor->getNodeType()->getLayout());
        $builder = $this->builderRegistry->get($formDescriptor->getNodeType()->getLayout());

        return $builder->build(
            $formDescriptor->getNodeType(),
            $layout,
            $formDescriptor->getFormView()
        );

        dump($nodeType, $layout, $builder);
        exit;
    }
}
