<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Menu\Application\Command\ItemStorage;
use Tulia\Cms\Menu\Application\Model\Item as ApplicationItem;
use Tulia\Cms\Menu\Application\Query\Finder\Model\Item as QueryItem;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemFormManager
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
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var ApplicationItem
     */
    private $item;

    /**
     * @var QueryItem
     */
    private $sourceItem;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param ItemStorage $itemStorage
     * @param QueryItem $sourceItem
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        ItemStorage $itemStorage,
        QueryItem $sourceItem
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->itemStorage    = $itemStorage;
        $this->sourceItem     = $sourceItem;
    }

    public function createCreateForm(): FormInterface
    {
        $this->item = ApplicationItem::fromQueryModel($this->sourceItem);

        return $this->getManager()->createForm(MenuItemForm::class, $this->item);
    }

    public function createEditForm(): FormInterface
    {
        $this->item = ApplicationItem::fromQueryModel($this->sourceItem);

        return $this->getManager()->createForm(MenuItemForm::class, $this->item, [ 'persist_mode' => 'edit' ]);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationItem $data */
        $data = $form->getData();

        if ($this->sourceItem) {
            $this->sourceItem->setId($data->getId());
        }

        $this->itemStorage->save($data);
    }

    public function getManager(): ManagerInterface
    {
        if ($this->manager) {
            return $this->manager;
        }

        return $this->manager = $this->managerFactory->getInstanceFor($this->item, ScopeEnum::BASKEND_EDIT);
    }
}
