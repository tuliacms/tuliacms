<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Model;

use Tulia\Cms\Website\Domain\WriteModel\Exception;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\WebsiteId;

/**
 * @author Adam Banaszkiewicz
 */
class Website
{
    private WebsiteId $id;
    private string $name;
    private string $backendPrefix;
    private bool $active = true;

    /**
     * @var Locale[]
     */
    private array $locales = [];

    public function __construct(string $id, string $name, string $backendPrefix, array $locales = [], bool $active = true)
    {
        $this->id = new WebsiteId($id);
        $this->name = $name;
        $this->backendPrefix = $backendPrefix;
        $this->active = $active;

        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }
    }

    public static function buildFromArray(array $data): self
    {
        $website = new self(
            $data['id'],
            $data['name'] ?? '',
            $data['backend_prefix'] ?? '/administrator'
        );

        foreach ($data['locales'] ?? [] as $locale) {
            $website->addLocale(new Locale(
                $locale['code'] ?? 'en_US',
                $locale['domain'] ?? 'localhost',
                $locale['domainDevelopment'] ?? 'localhost',
                $locale['localePrefix'] ?? null,
                $locale['pathPrefix'] ?? null,
                $locale['sslMode'] ?? null,
                $locale['isDefault'] ?? null,
            ));
        }

        return $website;
    }

    public function getId(): WebsiteId
    {
        return $this->id;
    }

    public function setId(WebsiteId $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }

    public function setBackendPrefix(string $backendPrefix): void
    {
        $this->backendPrefix = $backendPrefix;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    public function setLocales(array $locales): void
    {
        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }
    }

    /**
     * @param Locale $locale
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    public function addLocale(Locale $locale): void
    {
        $key = null;

        foreach ($this->locales as $duplicatedKey => $el) {
            if ($locale->getCode() === $el->getCode()) {
                $key = $duplicatedKey;
            }
        }

        $this->validateLocale($locale);

        if ($key !== null) {
            $this->locales[$key] = $locale;
        } else {
            $this->locales[] = $locale;
        }
    }

    /**
     * @param Locale $locale
     *
     * @throws Exception\LocaleNotExistsException
     */
//    public function removeLocale(Locale $locale): void
//    {
//        foreach ($this->locales as $el) {
//            if ($locale->getCode() === $el->getCode()) {
//                $this->locales->remove($el);
//                $this->recordThat(Event\LocaleRemoved::createFromAggregate($this->id, $locale));
//                return;
//            }
//        }
//
//        throw new Exception\LocaleNotExistsException(sprintf('Locale %s cannot be removed, because not exists in this website.', $locale->getCode()));
//    }

    /**
     * @param Locale $locale
     *
     * @throws Exception\LocaleNotExistsException
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    /*public function updateLocale(Locale $locale): void
    {
        foreach ($this->locales as $el) {
            if ($locale->getCode() === $el->getCode()) {
                if ($this->areLocalesSame($locale, $el) === false) {
                    $this->validateLocale($locale);
                    $this->locales->replaceByCode($locale);
                    $this->recordThat(Event\LocaleUpdated::createFromAggregate($this->id, $locale));
                }

                return;
            }
        }

        throw new Exception\LocaleNotExistsException(sprintf('Locale %s cannot be updated, because not exists in this website.', $locale->getCode()));
    }*/

    /**
     * @param Locale $one
     * @param Locale $two
     *
     * @return bool
     */
    /*private function areLocalesSame(Locale $one, Locale $two): bool
    {
        return $one->getCode() === $two->getCode()
            && $one->getDomain() === $two->getDomain()
            && $one->getLocalePrefix() === $two->getLocalePrefix()
            && $one->getPathPrefix() === $two->getPathPrefix()
            && $one->getSslMode() === $two->getSslMode()
            && $one->getIsDefault() === $two->getIsDefault()
            ;
    }*/

    /**
     * @param string $code
     *
     * @return bool
     */
    /*public function hasLocaleByCode(string $code): bool
    {
        foreach ($this->locales as $locale) {
            if ($locale->getCode() === $code) {
                return true;
            }
        }

        return false;
    }*/

    /**
     * @return LocaleCollection
     */
    /*public function getLocales(): LocaleCollection
    {
        $newCollection = new LocaleCollection();

        foreach ($this->locales as $locale) {
            $newCollection->append(clone $locale);
        }

        return $newCollection;
    }*/

    /**
     * @param string $name
     */
    /*public function rename(string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;
            $this->recordThat(new Event\WebsiteRenamed($this->id, $name));
        }
    }*/

    /**
     * @param string $backendPrefix
     */
    /*public function changeBackendPrefix(string $backendPrefix): void
    {
        if ($this->backendPrefix !== $backendPrefix) {
            $this->backendPrefix = $backendPrefix;
            $this->recordThat(new Event\BackendPrefixChanged($this->id, $backendPrefix));
        }
    }*/

    /**
     * @param Locale $locale
     *
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    private function validateLocale(Locale $locale): void
    {
        if ($locale->getPathPrefix() !== null) {
            if (! preg_match('/^\/{1}[a-z0-9-_]+$/', $locale->getPathPrefix())) {
                throw new Exception\PathPrefixInvalidException('PathPrefix must contain slash at the beggining and only those signs after slash: [a-z, 0-9, -, _].');
            }
        }

        if ($locale->getLocalePrefix() !== null) {
            if (! preg_match('/^\/{1}[a-z0-9-_]+$/', $locale->getLocalePrefix())) {
                throw new Exception\LocalePrefixInvalidException('LocalePrefix must contain slash at the beggining and only those signs after slash: [a-z, 0-9, -, _].');
            }
        }
    }
}
