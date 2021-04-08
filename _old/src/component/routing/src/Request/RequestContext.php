<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Request;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RequestContext implements RequestContextInterface
{
    /**
     * @var CurrentWebsiteInterface
     */
    protected $website;

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $pathinfo = '';

    /**
     * @var string
     */
    protected $contentPath = '';

    /**
     * @var string
     */
    protected $locale = '';

    /**
     * @var string
     */
    protected $contentLocale = '';

    /**
     * @var string
     */
    protected $defaultLocale = '';

    /**
     * @var bool
     */
    protected $backend;

    /**
     * {@inheritdoc}
     */
    public function getWebsite(): CurrentWebsiteInterface
    {
        return $this->website;
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsite(CurrentWebsiteInterface $website): void
    {
        $this->website = $website;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathinfo(): string
    {
        return $this->pathinfo;
    }

    /**
     * {@inheritdoc}
     */
    public function setPathinfo(string $pathinfo): void
    {
        $this->pathinfo = $pathinfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentPath(): string
    {
        return $this->contentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentPath(string $contentPath): void
    {
        $this->contentPath = $contentPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentLocale(): string
    {
        return $this->contentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentLocale(string $contentLocale): void
    {
        $this->contentLocale = $contentLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLocale(string $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackend(): bool
    {
        return $this->backend;
    }

    /**
     * {@inheritdoc}
     */
    public function setBackend(bool $backend): void
    {
        $this->backend = $backend;
    }
}
