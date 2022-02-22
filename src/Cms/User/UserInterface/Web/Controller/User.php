<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\SymfonyFieldBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\User\Application\UseCase\CreateUser;
use Tulia\Cms\User\Application\UseCase\UpdateUser;
use Tulia\Cms\User\Domain\WriteModel\Model\User as DomainModel;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class User extends AbstractController
{
    private FinderFactoryInterface $finderFactory;
    private ContentFormService $contentFormService;
    private UserRepositoryInterface $repository;
    private SymfonyFieldBuilder $fieldBuilder;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        ContentFormService $contentFormService,
        UserRepositoryInterface $repository,
        SymfonyFieldBuilder $fieldBuilder
    ) {
        $this->finderFactory = $finderFactory;
        $this->contentFormService = $contentFormService;
        $this->repository = $repository;
        $this->fieldBuilder = $fieldBuilder;
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
    public function create(Request $request, CreateUser $createUser)
    {
        $formDescriptor = $this->produceFormDescriptor();
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            $userId = ($createUser)($formDescriptor->getData());

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $userId ]);
        }

        return $this->view('@backend/user/user/create.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="content_builder_form_user")
     */
    public function edit(Request $request, string $id, UpdateUser $updateUser)
    {
        $user = $this->repository->find($id);

        if (! $user) {
            $this->setFlash('danger', $this->trans('userNotExists', [], 'users'));
            return $this->redirectToRoute('backend.user.list');
        }

        $formDescriptor = $this->produceFormDescriptor($user);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateUser)($user, $formDescriptor->getData());

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.user.edit', [ 'id' => $id ]);
        }

        return $this->view('@backend/user/user/edit.tpl', [
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="user.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $removedUsers = 0;

        // @todo Remove user avatar when delete user

        foreach ($request->request->get('ids') as $id) {
            $user = $this->repository->find($id);

            if ($user) {
                $this->repository->delete($user);
                $removedUsers++;
            }
        }

        if ($removedUsers) {
            $this->setFlash('success', $this->trans('selectedUsersWereDeleted', [], 'users'));
        }

        return $this->redirectToRoute('backend.user');
    }

    private function produceFormDescriptor(?DomainModel $user = null): ContentTypeFormDescriptor
    {
        $context = [
            'user_email' => $user === null ? null : $user->getEmail()
        ];

        $descriptor = $this->contentFormService->buildFormDescriptor(
            'user',
            $user ? $user->toArray() : [],
            $context
        );

        if ($user) {
            $passwordField = $descriptor->getContentType()->getField('password');
            $passwordField->removeConstraint('required');
            $passwordRepeatField = $descriptor->getContentType()->getField('password_repeat');
            $passwordRepeatField->removeConstraint('required');

            $descriptor->getFormBuilder()->remove('email');

            $this->fieldBuilder->buildFieldAndAddToBuilder($passwordField, $descriptor->getFormBuilder(), $descriptor->getContentType());
            $this->fieldBuilder->buildFieldAndAddToBuilder($passwordRepeatField, $descriptor->getFormBuilder(), $descriptor->getContentType());
        }

        return $descriptor;
    }
}
