<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Model\User as ApplicationUser;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\Exception\MultipleFetchException;
use Tulia\Cms\User\Query\Exception\QueryException;
use Tulia\Cms\User\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\User\UserInterface\Web\Form\MyAccount\MyAccountFormManagerFactory;
use Tulia\Cms\User\UserInterface\Web\Form\PasswordForm;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccount extends AbstractController
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;
    protected FinderFactoryInterface $finderFactory;
    protected ManagerFactoryInterface $managerFactory;

    /*public function __construct(
        AuthenticatedUserProviderInterface $authenticatedUserProvider,
        FinderFactoryInterface $finderFactory,
        ManagerFactoryInterface $managerFactory
    ) {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
        $this->finderFactory  = $finderFactory;
        $this->managerFactory = $managerFactory;
    }*/

    public function me()
    {
        return $this->view('@backend/user/me/me.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="my_account_form")
     */
    public function edit(Request $request, MyAccountFormManagerFactory $formFactory)
    {
        $user = $this->getUserInstance();
        $manager = $formFactory->create($user);

        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.me.edit');
        }

        return $this->view('@backend/user/me/edit.tpl', [
            'manager' => $manager->getManager(),
            'user'    => $this->authenticatedUserProvider->getUser(),
            'form'    => $form->createView(),
        ]);
    }

    public function personalization(): ViewInterface
    {
        return $this->view('@backend/user/me/personalization.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    /**
     * @CsrfToken(id="password_form")
     */
    public function password(Request $request, UserStorage $userStorage)
    {
        $user = $this->getUserInstance();
        $form = $this->createForm(PasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = ApplicationUser::fromQueryModel($user);
            $model->setPassword($form->getData()['password']);

            $userStorage->save($model);

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.me.password');
        }

        return $this->view('@backend/user/me/password.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return User
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getUserInstance(): User
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $this->authenticatedUserProvider->getUser()->getId()]);
        $finder->fetchRaw();

        $user = $finder->getResult()->first();

        if (! $user) {
            throw $this->createNotFoundException($this->trans('userNotFound', [], 'users'));
        }

        return $user;
    }
}
