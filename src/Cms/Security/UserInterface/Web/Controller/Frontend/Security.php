<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\UserInterface\Web\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;

/**
 * @author Adam Banaszkiewicz
 */
class Security extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // @todo Redirect to homepage when user is already logged in
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        return $this->render('@cms/security/login.tpl', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
