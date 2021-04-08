<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Cms\BackendMenu;

use Tulia\Cms\BackendMenu\Application\BuilderInterface;
use Tulia\Cms\BackendMenu\Application\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Application\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuBuilder implements BuilderInterface
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

    public function build(ItemRegistryInterface $registry): void
    {
        $registry->add('forms', [
            'label'    => $this->helper->trans('forms', [], 'forms'),
            'link'     => '#',
            'icon'     => 'fas fa-window-restore',
            'priority' => 2000,
        ]);

        $registry->add('forms.list', [
            'label'    => $this->helper->trans('list', [], 'forms'),
            'link'     => $this->helper->generateUrl('backend.form.homepage'),
            'parent'   => 'forms',
        ]);
    }
}
