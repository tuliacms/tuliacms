<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\Infrastructure\Cms\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultCmsMenuBuilder implements BuilderInterface
{
    protected BuilderHelperInterface $helper;

    public function __construct(BuilderHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * 5000 Dashboard
     * 4000 Contents
     * 2000 Administration
     *
     * @param ItemRegistryInterface $registry
     */
    public function build(ItemRegistryInterface $registry): void
    {
        $registry->add('dashboard', [
            'label'    => $this->helper->trans('dashboard'),
            'link'     => $this->helper->generateUrl('backend.homepage'),
            'priority' => 5000,
            'icon'     => 'fas fa-tachometer-alt',
            'active'   => false,
        ]);

        $registry->add('section_contents', [
            'type'     => 'section',
            'label'    => $this->helper->trans('contents'),
            'priority' => 4000,
        ]);

        $registry->add('section_administration', [
            'type'     => 'section',
            'label'    => $this->helper->trans('administration'),
            'priority' => 2000,
        ]);

        $registry->add('appearance', [
            'label'    => $this->helper->trans('appearance'),
            'link'     => '#',
            'priority' => 1900,
            'icon'     => 'fas fa-palette',
        ]);

        $registry->add('tools', [
            'label'    => $this->helper->trans('tools'),
            'link'     => $this->helper->generateUrl('backend.tools'),
            'priority' => 1700,
            'icon'     => 'fas fa-tools',
        ]);

        $registry->add('system', [
            'label'    => $this->helper->trans('system'),
            'link'     => $this->helper->generateUrl('backend.system'),
            'priority' => 1400,
            'icon'     => 'fas fa-dice-d6',
        ]);
    }
}
