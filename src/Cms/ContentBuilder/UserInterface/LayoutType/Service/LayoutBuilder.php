<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception\LayoutNotExists;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutBuilder
{
    private LayoutTypeBuilderRegistry $builderRegistry;
    private Configuration $config;

    public function __construct(LayoutTypeBuilderRegistry $builderRegistry, Configuration $config)
    {
        $this->builderRegistry = $builderRegistry;
        $this->config = $config;
    }

    /**
     * @throws LayoutNotExists
     */
    public function build(ContentTypeFormDescriptor $formDescriptor): View
    {
        $type = $formDescriptor->getContentType();

        return $this->builderRegistry
            ->get($this->config->getLayoutBuilder($type->getType()))
            ->editorView($type, $formDescriptor->getFormView());
    }
}
