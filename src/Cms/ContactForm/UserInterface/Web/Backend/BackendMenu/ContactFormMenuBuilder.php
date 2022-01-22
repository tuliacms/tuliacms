<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\BackendMenu;

use Tulia\Cms\BackendMenu\Ports\Domain\Builder\BuilderInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Helper\BuilderHelperInterface;
use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormMenuBuilder implements BuilderInterface
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
            'label'    => $this->helper->trans('forms', [], 'contact-form'),
            'link'     => '#',
            'icon'     => 'fas fa-window-restore',
            'priority' => 2000,
            'parent'   => 'section_contents',
        ]);

        $registry->add('forms.list', [
            'label'    => $this->helper->trans('list', [], 'contact-form'),
            'link'     => $this->helper->generateUrl('backend.contact_form'),
            'parent'   => 'forms',
        ]);
    }
}
