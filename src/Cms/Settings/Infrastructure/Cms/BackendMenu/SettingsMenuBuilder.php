<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Cms\BackendMenu;

use Tulia\Cms\BackendMenu\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsMenuBuilder implements BuilderInterface
{
    /**
     * @var BuilderHelperInterface
     */
    protected $helper;

    /**
     * @param BuilderHelperInterface $helper
     */
    public function __construct(BuilderHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param ItemRegistryInterface $registry
     */
    public function build(ItemRegistryInterface $registry): void
    {
        $registry->add('settings', [
            'label'    => $this->helper->trans('settings'),
            'link'     => $this->helper->generateUrl('backend.settings'),
            'priority' => 500,
            'icon'     => 'fas fa-cogs',
        ]);
    }
}
