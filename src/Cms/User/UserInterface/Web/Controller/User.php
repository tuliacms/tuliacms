<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Exception\TranslatableUserException;
use Tulia\Cms\User\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\Exception\MultipleFetchException;
use Tulia\Cms\User\Query\Exception\QueryException;
use Tulia\Cms\User\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\User\Query\Factory\UserFactoryInterface;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\Query\Model\User as QueryModelUser;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserFormManager;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserFormManagerFactory;
use Tulia\Component\CommandBus\Exception\MissingHandlerException;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractController
{
    protected FinderFactoryInterface $finderFactory;
    protected ManagerFactoryInterface $managerFactory;
    protected UserStorage $storage;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        ManagerFactoryInterface $managerFactory,
        UserStorage $storage
    ) {
        $this->finderFactory = $finderFactory;
        $this->managerFactory = $managerFactory;
        $this->storage = $storage;
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.user.list');
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/user/user/list.tpl', [
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @param UserFactoryInterface $userFactory
     * @param UserFormManagerFactory $formFactory
     * @return RedirectResponse|ViewInterface
     * @throws LogicException
     * @CsrfToken(id="user_form")
     */
    public function create(
        Request $request,
        UserFactoryInterface $userFactory,
        UserFormManagerFactory $formFactory
    ) {
        $user = $userFactory->createNew();
        $manager = $formFactory->create($user);

        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $user->getId() ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'manager' => $manager->getManager(),
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param UserFormManagerFactory $formFactory
     * @param string $id
     * @return RedirectResponse|ViewInterface
     * @throws MissingHandlerException
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     * @throws LogicException
     * @CsrfToken(id="user_form")
     */
    public function edit(
        Request $request,
        UserFormManagerFactory $formFactory,
        string $id
    ) {
        $user = $this->getUserById($id);
        $manager = $formFactory->create($user);

        $form = $manager->createForm(UserFormManager::MODE_UPDATE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $user->getId() ]);
        }

        return $this->view('@backend/user/user/edit.tpl', [
            'manager'  => $manager->getManager(),
            'user'     => $user,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws MissingHandlerException
     *
     * @CsrfToken(id="user.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $removedUsers = 0;

        throw new \Exception('Remove user avatar when delete user.');

        foreach ($request->request->get('ids') as $id) {
            try {
                $user = $this->getUserById($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            try {
                $this->repository->delete($user);
                $removedUsers++;
            } catch (TranslatableUserException $e) {
                $this->setFlash('warning', $this->transObject($e));
            }
        }

        if ($removedUsers) {
            $this->setFlash('success', $this->trans('selectedUsersWereDeleted', [], 'users'));
        }

        return $this->redirectToRoute('backend.user');
    }

    /**
     * @param string $id
     *
     * @return QueryModelUser
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getUserById(string $id): QueryModelUser
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $user = $finder->getResult()->first();

        if (! $user) {
            throw $this->createNotFoundException($this->trans('userNotFound', [], 'users'));
        }

        return $user;
    }
}
