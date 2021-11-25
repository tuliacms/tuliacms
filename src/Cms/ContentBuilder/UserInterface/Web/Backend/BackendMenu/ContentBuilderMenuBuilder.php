<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    public function __construct(
        BuilderHelperInterface $helper
    ) {
        $this->helper = $helper;
    }

    public function build(ItemRegistryInterface $registry): void
    {
        $registry->add('content_model', [
            'label' => $this->helper->trans('contentModel', [], 'content_builder'),
            'link'  => $this->helper->generateUrl('backend.content_model.homepage'),
            'icon'  => 'fas fa-box',
            'parent'   => 'section_administration',
        ]);
    }
}
