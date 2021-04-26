<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Exception\LocaleNotExistsException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website implements WebsiteInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $backendPrefix;

    /**
     * @var array|LocaleInterface[]
     */
    protected $locales = [];

    /**
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * @param string $id
     * @param array $locales
     * @param LocaleInterface $locale
     * @param string $backendPrefix
     * @param string $name
     */
    public function __construct(string $id, array $locales, LocaleInterface $locale, string $backendPrefix = '/administrator', string $name = '')
    {
        $this->id = $id;
        $this->locales = $locales;
        $this->locale = $locale;
        $this->backendPrefix = $backendPrefix;
        $this->name = $name;
    }

    public function __clone()
    {
        $locales = [];

        foreach ($this->locales as $locale) {
            $newLocale = clone $locale;
            $locales[] = $newLocale;

            if ($locale === $this->locale) {
                $this->locale = $newLocale;
            }
        }

        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public static function withNewLocale(WebsiteInterface $website, string $newLocale): WebsiteInterface
    {
        $cloned = clone $website;

        foreach ($cloned->locales as $locale) {
            if ($locale->getCode() === $newLocale) {
                $cloned->locale = $locale;
            }
        }

        return $cloned;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale(): LocaleInterface
    {
        foreach ($this->locales as $locale) {
            if ($locale->isDefault()) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException('Default locale not exists for this website.');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleByCode(string $code): LocaleInterface
    {
        foreach ($this->locales as $locale) {
            if ($locale->getCode() === $code) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException(sprintf('Locale "%s" not exists in this website.', $code));
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        if ($locale->getSslMode() === SslModeEnum::FORCE_SSL) {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $locale->getLocalePrefix() . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendAddress($locale = null): string
    {
        $locale = $this->resolveLocale($locale);

        if ($locale->getSslMode() === SslModeEnum::FORCE_SSL) {
            return 'https://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
        }

        return 'http://' . $locale->getDomain() . $locale->getPathPrefix() . $this->backendPrefix . $locale->getLocalePrefix() . '/';
    }

    /**
     * @param string|LocaleInterface $locale
     * @return LocaleInterface
     * @throws LocaleNotExistsException
     */
    private function resolveLocale($locale): LocaleInterface
    {
        if ($locale === null) {
            return $this->getDefaultLocale();
        } elseif ($locale instanceof LocaleInterface) {
            return $locale;
        } elseif (\is_string($locale)) {
            return $this->getLocaleByCode($locale);
        } else {
            throw new LocaleNotExistsException(sprintf('Locale must be a locale code string or instance of %s.', LocaleInterface::class));
        }
    }
}
