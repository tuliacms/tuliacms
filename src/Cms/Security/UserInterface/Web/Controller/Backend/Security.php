<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;

/**
 * @author Adam Banaszkiewicz
 */
class Security extends AbstractController
{
    /**
     * @return ViewInterface|RedirectResponse
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('backend_homepage');
        }

        return $this->view('@backend/security/login.tpl', [
            'bgImages'      => $this->getCollection(),
            'last_username' => $request->query->get('username', $authenticationUtils->getLastUsername()),
            'error'         => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function getCollection(): array
    {
        $filepath = $this->getParameter('kernel.public_dir').'/assets/core/backend/login/login-bg/collection.json';

        return json_decode(file_get_contents($filepath));
    }
}
