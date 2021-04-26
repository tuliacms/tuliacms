<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Menu\Application\Command\ItemStorage;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item as QueryItem;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemFormManagerFactory
{
    /**
     * @var ManagerFactoryInterface
     */
    private $managerFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ItemStorage
     */
    private $itemStorage;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param ItemStorage $itemStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        ItemStorage $itemStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->itemStorage    = $itemStorage;
    }

    public function create(?QueryItem $item = null): MenuItemFormManager
    {
        return new MenuItemFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->itemStorage,
            $item
        );
    }
}
