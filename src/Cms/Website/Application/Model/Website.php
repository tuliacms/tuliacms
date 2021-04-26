<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Model;

use Tulia\Cms\Website\Query\Model\Website as QueryModelWebsite;

/**
 * @author Adam Banaszkiewicz
 */
class Website
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var null|string
     */
    private $backendPrefix;

    /**
     * @var LocaleCollection|Locale[]|array|iterable
     */
    private $locales;

    /**
     * @param string $id
     * @param string|null $name
     * @param string $backendPrefix
     * @param LocaleCollection $locales
     */
    public function __construct(string $id, string $name = null, string $backendPrefix = '/administrator', LocaleCollection $locales = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->backendPrefix = $backendPrefix;
        $this->locales = $locales ?? new LocaleCollection();
    }

    public static function fromQueryModel(QueryModelWebsite $website): self
    {
        $locales = [];

        foreach ($website->getLocales() as $locale) {
            $locales[] = Locale::fromQueryModel($locale);
        }

        return new self(
            $website->getId(),
            $website->getName(),
            $website->getBackendPrefix(),
            new LocaleCollection($locales)
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getBackendPrefix(): ?string
    {
        return $this->backendPrefix;
    }

    /**
     * @param null|string $backendPrefix
     */
    public function setBackendPrefix(?string $backendPrefix): void
    {
        $this->backendPrefix = $backendPrefix;
    }

    /**
     * @return LocaleCollection
     */
    public function getLocales(): LocaleCollection
    {
        return $this->locales;
    }

    /**
     * @param LocaleCollection $locales
     */
    public function setLocales(LocaleCollection $locales): void
    {
        $this->locales = $locales;
    }

    /**
     * @param Locale $locale
     */
    public function addLocale(Locale $locale): void
    {
        $this->locales->append($locale);
    }

    /**
     * @param Locale $locale
     */
    public function removeLocale(Locale $locale): void
    {
        $this->locales->remove($locale);
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function hasLocaleByCode(string $code): bool
    {
        foreach ($this->locales as $locale) {
            if ($locale->getCode() === $code) {
                return true;
            }
        }

        return false;
    }
}
