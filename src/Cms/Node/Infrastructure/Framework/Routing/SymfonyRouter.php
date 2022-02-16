<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Router;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyRouter implements RouterInterface, RequestMatcherInterface
{
    private FrontendRouteSuffixResolver $frontendRouteSuffixResolver;

    private Router $contentTypeRouter;

    private ?RequestContext $context = null;

    public function __construct(
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver,
        Router $contentTypeRouter
    ) {
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
        $this->contentTypeRouter = $contentTypeRouter;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        // Dynamic routing don't have any static collection
        return new RouteCollection();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): ?string
    {
        if (strncmp($name, 'node.', 5) !== 0) {
            return null;
        }

        [, $type, $identity] = explode('.', $name);

        $parameters = array_merge([
            // @todo Fix routing locales
            '_locale' => 'en_EN',//$this->getContext()->getParameter('_content_locale'),
        ], $parameters);

        $path = $this->contentTypeRouter->generate($type, $identity, $parameters);

        return $this->frontendRouteSuffixResolver->appendSuffix($path);
    }

    public function matchRequest(Request $request): array
    {
        return $this->match($request->attributes->get('_content_path', $request->getPathInfo()));
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo): array
    {
        $pathinfo = urldecode($pathinfo);
        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);

        $parameters = $this->contentTypeRouter->match($pathinfo, [
            // @todo Fix routing locales
            '_locale' => 'en_EN',//$this->getContext()->getParameter('_content_locale'),
        ]);

        if ($parameters === []) {
            throw new ResourceNotFoundException('Node not found with given path.');
        }

        return $parameters;
    }
}
