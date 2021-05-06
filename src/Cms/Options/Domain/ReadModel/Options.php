<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\ReadModel;

use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\ReadModel\OptionsFinderInterface;
use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Options
{
    private OptionsFinderInterface $finder;
    private OptionsRepositoryInterface $repository;
    private CurrentWebsiteInterface $currentWebsite;
    private array $cache = [];
    private array $autoloaded = [];

    public function __construct(
        OptionsFinderInterface $finder,
        OptionsRepositoryInterface $repository,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->finder = $finder;
        $this->repository = $repository;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @param string|null $locale
     * @param string|null $websiteId
     * @return mixed
     */
    public function get(string $name, $default = null, ?string $locale = null, ?string $websiteId = null)
    {
        $locale = $this->resolveLocale($locale);
        $websiteId = $this->resolveWebsite($websiteId);

        $this->autoload($websiteId, $locale);

        if (isset($this->cache[$websiteId][$locale][$name])) {
            return $this->cache[$websiteId][$locale][$name];
        }

        return $this->cache[$websiteId][$locale][$name] = $this->finder->findByName(
            $name,
            $locale,
            $websiteId
        ) ?? $default;
    }

    public function preload(array $names, ?string $locale = null, ?string $websiteId = null): void
    {
        $values = $this->finder->findBulkByName(
            $names,
            $this->resolveLocale($locale),
            $this->resolveWebsite($websiteId)
        );

        foreach ($values as $name => $value) {
            $this->cache[$websiteId][$locale][$name] = $value;
        }
    }

    private function resolveWebsite(?string $websiteId = null): string
    {
        if (! $websiteId) {
            return $this->currentWebsite->getId();
        }

        return $websiteId;
    }

    private function resolveLocale(?string $locale = null): string
    {
        if (! $locale) {
            return $this->currentWebsite->getLocale()->getCode();
        }

        return $locale;
    }

    private function autoload(string $websiteId, string $locale): void
    {
        if (isset($this->autoloaded[$websiteId][$locale])) {
            return;
        }

        $this->cache[$websiteId][$locale] = $this->finder->autoload($locale, $websiteId);
        $this->autoloaded[$websiteId][$locale] = true;
    }
}
