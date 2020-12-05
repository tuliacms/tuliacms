<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Router implements RouterInterface
{
    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var RequestContextInterface
     */
    protected $requestContext;

    /**
     * @param CurrentWebsiteInterface $currentWebsite
     * @param MatcherInterface $matcher
     * @param GeneratorInterface $generator
     * @param RequestContextInterface $requestContext
     */
    public function __construct(
        CurrentWebsiteInterface $currentWebsite,
        MatcherInterface $matcher,
        GeneratorInterface $generator,
        RequestContextInterface $requestContext
    ) {
        $this->matcher        = $matcher;
        $this->currentWebsite = $currentWebsite;
        $this->generator      = $generator;
        $this->requestContext = $requestContext;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo, ?RequestContextInterface $context = null): ?array
    {
        return $this->matcher->match($pathinfo, $context ?? $this->requestContext);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $params = [], int $type = RouterInterface::TYPE_PATH, ?RequestContextInterface $context = null): string
    {
        $params['_locale_prefix'] = $this->resolveLocalePrefix($params);

        $path = $this->generator->generate($name, $params, $context ?? $this->requestContext);

        if ($type === RouterInterface::TYPE_PATH) {
            return $this->path($path, $params);
        }

        return $this->url($path, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function path(string $path, array $params = []): string
    {
        return $this->currentWebsite->getPathPrefix() . $path;
    }

    /**
     * {@inheritdoc}
     */
    public function url(string $path, array $params = []): string
    {
        if (isset($params['_locale'])) {
            $locale = $this->currentWebsite->getLocaleByCode($params['_locale']);
        } else {
            $locale = $this->currentWebsite->getLocale();
        }

        return sprintf(
            '%s://%s%s%s',
            $this->currentWebsite->getScheme(),
            $locale->getDomain(),
            $locale->getPathPrefix(),
            $path
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestContext(): RequestContextInterface
    {
        return $this->requestContext;
    }

    private function resolveLocalePrefix(array $params): string
    {
        if (isset($params['_locale']) === false) {
            $params['_locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        /** @var LocaleInterface $locale */
        foreach ($this->currentWebsite->getLocales() as $locale) {
            if ($locale->getCode() === $params['_locale']) {
                return (string) $locale->getLocalePrefix();
            }
        }

        return '';
    }
}
