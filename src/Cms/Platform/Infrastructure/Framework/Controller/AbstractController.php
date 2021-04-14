<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Component\CommandBus\CommandBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractController extends SymfonyController
{
    /*public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            RequestStack::class,
            TranslatorInterface::class,
            RouterInterface::class,
            UuidGeneratorInterface::class,
            FormFactoryInterface::class,
            DocumentInterface::class,
            AuthorizationCheckerInterface::class,
            CommandBusInterface::class,
            CsrfTokenManagerInterface::class,
            AuthenticatedUserProviderInterface::class,
            ContainerBagInterface::class
        ];
    }*/

    public function setFlash(string $type, string $message): void
    {
        /** @var Request $request */
        $request = $this->container->get(RequestStack::class)->getMasterRequest();

        if ($request) {
            $request->getSession()->getFlashBag()->add($type, $message);
        }
    }

    public function getFlashes(): array
    {
        /** @var Request $request */
        $request = $this->container->get(RequestStack::class)->getMasterRequest();

        if ($request) {
            return $request->getSession()->getFlashBag()->all();
        }

        return [];
    }

    /**
     * @param string|array $views
     * @param array $data
     *
     * @return ViewInterface
     */
    public function view($views, array $data = []): ViewInterface
    {
        return new View($views, $data);
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    public function transObject(TranslatableInterface $translatable, string $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans(
            $translatable->getMessage(),
            $translatable->getParameters(),
            $translatable->getDomain(),
            $locale
        );
    }

    public function responseJson(array $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    public function uuid(): string
    {
        return $this->container->get(UuidGeneratorInterface::class)->generate();
    }

    public function getDocument(): DocumentInterface
    {
        return $this->container->get(DocumentInterface::class);
    }

    public function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function getCommandBus(): CommandBusInterface
    {
        return $this->container->get(CommandBusInterface::class);
    }

    /**
     * @param string $id
     * @param null|string $value
     * @throws RequestCsrfTokenException
     */
    public function validateCsrfToken(string $id, ?string $value = null): void
    {
        $value = $value ?? $this->container->get(RequestStack::class)->getMasterRequest()->get('_token');

        if ($this->isCsrfTokenValid($id, $value) === false) {
            throw new RequestCsrfTokenException('Token '.$id.' not found in Request.');
        }
    }
}
