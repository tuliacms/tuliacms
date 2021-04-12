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
use Tulia\Framework\Http\Request;
use Tulia\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Framework\Translation\TranslatableInterface;
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

    /**
     * @param string|null $type
     * @param array|object $data
     * @param array $options
     * @return Form
     */
    public function createForm(string $type = null, $data = null, array $options = []): Form
    {
        return $this->container->get(FormFactoryInterface::class)->create($type, $data, $options);
    }

    public function getDocument(): DocumentInterface
    {
        return $this->container->get(DocumentInterface::class);
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    public function isGranted($attribute, $subject = null): bool
    {
        return $this->container->get(AuthorizationCheckerInterface::class)->isGranted($attribute, $subject);
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param string $message
     */
    public function denyAccessUnlessGranted($attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (! $this->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    public function createAccessDeniedException(string $message = 'Access Denied.', \Throwable $previous = null): AccessDeniedException
    {
        return new AccessDeniedException($message, $previous);
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

    protected function isCsrfTokenValid(string $id, ?string $token): bool
    {
        return $this->container->get(CsrfTokenManagerInterface::class)->isTokenValid(new CsrfToken($id, $token));
    }

    protected function getUser(): User
    {
        return $this->container->get(AuthenticatedUserProviderInterface::class)->getUser();
    }

    protected function generateUrl(string $name, array $params = [], int $type = RouterInterface::TYPE_PATH): string
    {
        return $this->container->get(RouterInterface::class)->generate($name, $params, $type);
    }
}
