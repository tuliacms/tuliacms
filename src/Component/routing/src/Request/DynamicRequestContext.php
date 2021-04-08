<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Request;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class DynamicRequestContext extends RequestContext
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     * @param CurrentWebsiteInterface $website
     */
    public function __construct(RequestStack $requestStack, CurrentWebsiteInterface $website)
    {
        $this->requestStack = $requestStack;
        $this->website      = $website;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        if ($this->host === '') {
            $this->host = $this->getRequest()->getHost();
        }

        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathinfo(): string
    {
        if ($this->pathinfo === '') {
            $this->pathinfo = $this->getRequest()->getPathInfo();
        }

        return $this->pathinfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentPath(): string
    {
        if ($this->contentPath === '') {
            $this->contentPath = $this->getRequest()->getContentPath();
        }

        return $this->contentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        if ($this->locale === '') {
            $this->locale = $this->getRequest()->getLocale();
        }

        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentLocale(): string
    {
        if ($this->contentLocale === '') {
            $this->contentLocale = $this->getRequest()->getContentLocale();
        }

        return $this->contentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale(): string
    {
        if ($this->defaultLocale === '') {
            $this->defaultLocale = $this->getRequest()->getDefaultLocale();
        }

        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackend(): bool
    {
        if ($this->backend === null) {
            $this->backend = $this->getRequest()->isBackend();
        }

        return $this->backend;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        if ($this->method === '') {
            $this->method = $this->getRequest()->getMethod();
        }

        return $this->method;
    }

    private function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
