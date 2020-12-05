<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Psr\Container\ContainerInterface;
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
use Tulia\Component\DependencyInjection\ContainerAwareInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;
use Tulia\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Framework\Translation\TranslatableInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->container->get('parameters_bag')->getParameter($name);
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function setFlash(string $type, string $message): void
    {
        /** @var Request $request */
        $request = $this->container->get(RequestStack::class)->getMasterRequest();

        if ($request) {
            $request->getSession()->getFlashBag()->add($type, $message);
        }
    }

    /**
     * @return array
     */
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
     * @param array        $data
     *
     * @return ViewInterface
     */
    public function view($views, array $data = []): ViewInterface
    {
        return new View($views, $data);
    }

    /**
     * @param string|null $id
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     *
     * @return string|null
     */
    public function trans(?string $id, array $parameters = [], $domain = null, $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param TranslatableInterface $translatable
     * @param null $locale
     *
     * @return string|null
     */
    public function transObject(TranslatableInterface $translatable, $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans(
            $translatable->getMessage(),
            $translatable->getParameters(),
            $translatable->getDomain(),
            $locale
        );
    }

    /**
     * @param string $name
     * @param array $params
     * @param int $responseCode
     *
     * @return RedirectResponse
     */
    public function redirect(string $name, array $params = [], int $responseCode = Response::HTTP_SEE_OTHER): RedirectResponse
    {
        $url = $this->container->get(RouterInterface::class)->generate($name, $params, RouterInterface::TYPE_URL);

        return new RedirectResponse($url, $responseCode);
    }

    /**
     * @param string $url
     * @param int $responseCode
     *
     * @return RedirectResponse
     */
    public function redirectToUrl(string $url, int $responseCode = Response::HTTP_SEE_OTHER): RedirectResponse
    {
        return new RedirectResponse($url, $responseCode);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param bool $json
     *
     * @return JsonResponse
     */
    public function responseJson($data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    /**
     * @return string
     */
    public function uuid(): string
    {
        return $this->container->get(UuidGeneratorInterface::class)->generate();
    }

    /**
     * @param string|null $type
     * @param null $data
     * @param array $options
     *
     * @return Form
     */
    public function createForm(string $type = null, $data = null, array $options = []): Form
    {
        return $this->container->get(FormFactoryInterface::class)->create($type, $data, $options);
    }

    /**
     * @return DocumentInterface
     */
    public function getDocument(): DocumentInterface
    {
        return $this->container->get(DocumentInterface::class);
    }

    /**
     * @param $attribute
     * @param null $subject
     *
     * @return bool
     */
    public function isGranted($attribute, $subject = null): bool
    {
        return $this->container->get(AuthorizationCheckerInterface::class)->isGranted($attribute, $subject);
    }

    /**
     * @param $attributes
     * @param null $subject
     * @param string $message
     */
    public function denyAccessUnlessGranted($attributes, $subject = null, string $message = 'Access Denied.'): void
    {
        if (! $this->isGranted($attributes, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attributes);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * @param string $message
     * @param \Throwable|null $previous
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, 0, $previous);
    }

    /**
     * @param string $message
     * @param \Throwable|null $previous
     *
     * @return AccessDeniedException
     */
    public function createAccessDeniedException(string $message = 'Access Denied.', \Throwable $previous = null): AccessDeniedException
    {
        return new AccessDeniedException($message, $previous);
    }

    /**
     * @return CommandBusInterface
     */
    public function getCommandBus(): CommandBusInterface
    {
        return $this->container->get(CommandBusInterface::class);
    }

    /**
     * @param $id
     * @param null $value
     *
     * @throws RequestCsrfTokenException
     */
    public function validateCsrfToken($id, $value = null): void
    {
        $value = $value ?? $this->container->get(RequestStack::class)->getMasterRequest()->get('_token');

        if ($this->isCsrfTokenValid($id, $value) === false) {
            throw new RequestCsrfTokenException('Token '.$id.' not found in Request.');
        }
    }

    /**
     * @param string $id
     * @param string|null $token
     *
     * @return bool
     */
    protected function isCsrfTokenValid(string $id, ?string $token): bool
    {
        return $this->container->get(CsrfTokenManagerInterface::class)->isTokenValid(new CsrfToken($id, $token));
    }

    /**
     * @return User
     */
    protected function getUser(): User
    {
        return $this->container->get(AuthenticatedUserProviderInterface::class)->getUser();
    }

    /**
     * @param string $name
     * @param array $params
     * @param int $type
     *
     * @return string
     */
    protected function generateUrl(string $name, array $params = [], int $type = RouterInterface::TYPE_PATH): string
    {
        return $this->container->get(RouterInterface::class)->generate($name, $params, $type);
    }
}
