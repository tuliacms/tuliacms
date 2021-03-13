<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Framework\Security\Authentication\Exception\LoginException;
use Tulia\Framework\Security\Authentication\LoginCredentials\LoginFormCredentials;
use Tulia\Framework\Security\Authentication\LoginServiceInterface;
use Tulia\Framework\Security\Authentication\LogoutServiceInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Security extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return ViewInterface|RedirectResponse
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        if ($this->isLoggedIn()) {
            return $this->redirect('backend');
        }

        return $this->view('@backend/security/login.tpl', [
            'last_username' => $request->query->get('username', $authenticationUtils->getLastUsername()),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
            'bgImages'      => $this->getCollection(),
        ]);
    }

    /**
     * @param Request $request
     * @param LoginServiceInterface $loginService
     *
     * @return RedirectResponse
     *
     * @CsrfToken(id="authenticate")
     */
    public function loginProcess(Request $request, LoginServiceInterface $loginService): RedirectResponse
    {
        try {
            $credentials = new LoginFormCredentials($request->get('username'), $request->get('password'));

            $loginService->login($credentials);

            return $this->redirect('backend');
        } catch (LoginException $e) {
            return $this->redirect('backend.login');
        }
    }

    /**
     * @param LogoutServiceInterface $logoutService
     *
     * @return RedirectResponse
     */
    public function logout(LogoutServiceInterface $logoutService): RedirectResponse
    {
        $logoutService->logout();

        return $this->redirect('backend.login');
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        $filepath = $this->getParameter('kernel.project_dir').'/public/assets/core/backend/login/login-bg/collection.json';

        return json_decode(file_get_contents($filepath));
    }
}
