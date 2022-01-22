<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Component\Templating\View;

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
    public function build(ContentTypeFormDescriptor $formDescriptor): View
    {
        $type = $formDescriptor->getContentType();

        return $this->builderRegistry
            ->get($type->getLayout()->getBuilder())
            ->editorView($type, $formDescriptor->getFormView());
    }
}
