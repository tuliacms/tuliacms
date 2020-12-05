<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UI\Web\Form\MyAccount;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Model\User as ApplicationUser;
use Tulia\Cms\User\Query\Model\User as QueryUser;
use Tulia\Cms\User\UI\Web\Form\ScopeEnum;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccountFormManager
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
     * @var UserStorage
     */
    private $userStorage;

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var ApplicationUser
     */
    private $user;

    /**
     * @var QueryUser
     */
    private $sourceUser;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param UserStorage $userStorage
     * @param QueryUser $sourceUser
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        UserStorage $userStorage,
        QueryUser $sourceUser
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->userStorage    = $userStorage;
        $this->sourceUser     = $sourceUser;
    }

    public function createForm(): FormInterface
    {
        $this->user = ApplicationUser::fromQueryModel($this->sourceUser);

        return $this->getManager()->createForm(MyAccountForm::class, $this->user);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationUser $data */
        $data = $form->getData();

        $this->sourceUser->setId($data->getId());

        $this->userStorage->save($data);
    }

    public function getManager(): ManagerInterface
    {
        if ($this->manager) {
            return $this->manager;
        }

        return $this->manager = $this->managerFactory->getInstanceFor($this->user, ScopeEnum::BACKEND_MY_ACCOUNT);
    }
}
