<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\CommandBus\CommandBusInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractController extends SymfonyController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            RequestStack::class,
            TranslatorInterface::class,
            //RouterInterface::class,
            //UuidGeneratorInterface::class,
            //FormFactoryInterface::class,
            DocumentInterface::class,
            //AuthorizationCheckerInterface::class,
            //CommandBusInterface::class,
            //CsrfTokenManagerInterface::class,
            //AuthenticatedUserProviderInterface::class,
            //ContainerBagInterface::class
        ];
    }

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

    public function isHomepage(Request $request): bool
    {
        return $request->getUri() === $this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
