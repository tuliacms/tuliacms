<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UI\Web\Form\MyAccount;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Query\Model\User as QueryUser;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccountFormManagerFactory
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
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param UserStorage $userStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        UserStorage $userStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->userStorage    = $userStorage;
    }

    public function create(?QueryUser $user = null): MyAccountFormManager
    {
        return new MyAccountFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->userStorage,
            $user
        );
    }
}