<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Application\UseCase\ChangePassword;
use Tulia\Cms\User\Application\UseCase\UpdateMyAccount;
use Tulia\Cms\User\Domain\WriteModel\UserRepositoryInterface;
use Tulia\Cms\User\UserInterface\Web\Form\PasswordForm;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccount extends AbstractController
{
    private AuthenticatedUserProviderInterface $authenticatedUserProvider;
    private UserRepositoryInterface $userRepository;
    private ContentFormService $contentFormService;

    public function __construct(
        AuthenticatedUserProviderInterface $authenticatedUserProvider,
        UserRepositoryInterface $userRepository,
        ContentFormService $contentFormService
    ) {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
        $this->userRepository = $userRepository;
        $this->contentFormService = $contentFormService;
    }

    public function me(): ViewInterface
    {
        return $this->view('@backend/user/me/me.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    public function personalization(): ViewInterface
    {
        return $this->view('@backend/user/me/personalization.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_my_account")
     */
    public function edit(Request $request, UpdateMyAccount $updateMyAccount)
    {
        $user = $this->userRepository->find($this->authenticatedUserProvider->getUser()->getId());

        if (!$user) {
            return $this->redirectToRoute('backend.homepage');
        }

        $data = $user->toArray();
        $data['remove_avatar'] = '0';

        $formDescriptor = $this->contentFormService->buildFormDescriptor('my_account', $data);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            ($updateMyAccount)($user, $formDescriptor->getData());

            $this->setFlash('success', $this->trans('userSaved', [], 'users'));
            return $this->redirectToRoute('backend.me.edit');
        }

        return $this->view('@backend/user/me/edit.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @CsrfToken(id="password_form")
     */
    public function password(Request $request, ChangePassword $changePassword)
    {
        $user = $this->userRepository->find($this->authenticatedUserProvider->getUser()->getId());

        if (!$user) {
            return $this->redirectToRoute('backend.homepage');
        }

        $form = $this->createForm(PasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->authenticatedUserProvider->isPasswordValid($data['current_password']) === false) {
                $this->setFlash('danger', $this->trans('pleaseTypeValidCurrentPasswordToDoThisOperation', [], 'users'));
                return $this->redirectToRoute('backend.me.password');
            }

            ($changePassword)($user, $data['new_password']);

            $this->setFlash('danger', $this->trans('passwordChangedSuccessfully', [], 'users'));
            return $this->redirectToRoute('backend.logout');
        }

        return $this->view('@backend/user/me/password.tpl', [
            'user' => $this->authenticatedUserProvider->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
