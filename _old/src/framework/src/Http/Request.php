<?php

declare(strict_types=1);

namespace Tulia\Framework\Http;

use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * @author Adam Banaszkiewicz
 */
class Request extends BaseRequest
{
    /**
     * @var string
     */
    protected $contentLocale = 'en_US';

    /**
     * {@inheritdoc}
     */
    protected $defaultLocale = 'en_US';

    /**
     * Returns directory name that exists in URL.
     * @return string
     */
    public function getDirectory(): string
    {
        return str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    }

    /**
     * @return mixed
     */
    public function getContentLocale(): string
    {
        return $this->contentLocale;
    }

    /**
     * @param mixed $contentLocale
     */
    public function setContentLocale(string $contentLocale): void
    {
        $this->contentLocale = $contentLocale;
    }

    public function isDefaultContentLocale(): bool
    {
        return $this->contentLocale === $this->defaultLocale;
    }

    /**
     * Shortcut to detect ajax requests.
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * @return bool
     */
    public function isBackend(): bool
    {
        return $this->attributes->get('_is_backend');
    }

    /**
     * @param bool $isBackend
     */
    public function setIsBackend(bool $isBackend): void
    {
        $this->attributes->set('_is_backend', $isBackend);
    }

    /**
     * Return path without any backend segment and locale segment in it.
     * Path is defined in arguments only when BackendResolver and LocaleResolver
     * are called and are called in proper order. Otherwise returned is result of
     * getPathInfo() method.
     *
     * @return string
     */
    public function getContentPath(): string
    {
        return (string) $this->attributes->get('_content_path', $this->getPathInfo());
    }
}
