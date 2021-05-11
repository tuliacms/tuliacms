<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
use Tulia\Cms\User\UserInterface\Web\Form\MyAccount\MyAccountForm;
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
    private UserStorage $userStorage;

    public function __construct(
        AuthenticatedUserProviderInterface $authenticatedUserProvider,
        FinderFactoryInterface $finderFactory,
        ManagerFactoryInterface $managerFactory,
        UserStorage $userStorage
    ) {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
        $this->finderFactory  = $finderFactory;
        $this->managerFactory = $managerFactory;
        $this->userStorage = $userStorage;
    }

    public function me(): ViewInterface
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
    public function edit(Request $request)
    {
        $user = $this->getUserInstance();
        $model = ApplicationUser::fromQueryModel($user);

        $form = $this->createForm(MyAccountForm::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userStorage->save($form->getData());

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.me.edit');
        }

        return $this->view('@backend/user/me/edit.tpl', [
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
    public function password(Request $request)
    {
        $user = $this->getUserInstance();
        $form = $this->createForm(PasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = ApplicationUser::fromQueryModel($user);
            $model->setPassword($form->getData()['password']);

            $this->userStorage->save($model);

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
