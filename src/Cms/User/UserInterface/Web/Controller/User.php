<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Exception\TranslatableUserException;
use Tulia\Cms\User\Application\Model\User as ApplicationUser;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\Exception\MultipleFetchException;
use Tulia\Cms\User\Query\Exception\QueryException;
use Tulia\Cms\User\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\Query\Model\User as QueryModelUser;
use Tulia\Cms\User\Domain\WriteModel\Model\User as DomainModel;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractController
{
    protected FinderFactoryInterface $finderFactory;
    protected UserStorage $storage;
    private ContentFormService $contentFormService;
    private UserRepositoryInterface $repository;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        ContentFormService $contentFormService,
        UserRepositoryInterface $repository
    ) {
        $this->finderFactory = $finderFactory;
        $this->contentFormService = $contentFormService;
        $this->repository = $repository;
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
     * @CsrfToken(id="content_builder_form_user")
     */
    public function create(Request $request)
    {
        $formDescriptor = $this->produceFormDescriptor($request);

        /*$form = $this->createForm(UserForm::class, [], [ 'password_required' => true ]);
        $form->handleRequest($request);*/

        if ($formDescriptor->isFormValid()) {
            dump($formDescriptor->getData());
            //$this->storage->save($formDescriptor->getData());

            //$this->setFlash('success', $this->trans('userSaved', [], 'users'));
            //return $this->redirectToRoute('backend.user.edit', [ 'id' => $user->getId() ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function edit(Request $request, string $id)
    {
        $user = $this->getUserById($id);
        $model = ApplicationUser::fromQueryModel($user);

        $form = $this->createForm(UserForm::class, $model, [ 'password_required' => false ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->storage->save($form->getData());

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $user->getId() ]);
        }

        return $this->view('@backend/user/user/edit.tpl', [
            'user' => $user,
            'form' => $form->createView(),
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

    private function produceFormDescriptor(Request $request, ?DomainModel $user = null): ContentTypeFormDescriptor
    {
        $data = [];

        if ($user) {
            $data = array_merge(
                [
                    'email' => $user->getEmail(),
                    'password' => $user->getPassword(),
                    'enabled' => $user->isEnabled(),
                    'roles' => $user->getRoles(),
                ],
                $user->getAttributes()
            );
        }

        return $this->contentFormService->buildFormDescriptor('user', $data, $request);
    }
}
