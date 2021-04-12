<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

use Tulia\Cms\Options\Infrastructure\Persistence\ReadModel\Options\OptionsFinderInterface;
use Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository\OptionsRepositoryInterface;
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

    public function __construct(OptionsFinderInterface $finder, OptionsRepositoryInterface $repository, CurrentWebsiteInterface $currentWebsite)
    {
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

        if (isset($this->cache[$websiteId][$locale][$name])) {
            return $this->cache[$websiteId][$locale][$name];
        }

        return $this->cache[$websiteId][$locale][$name] = $this->finder->findByName(
            $name,
            $locale,
            $websiteId
        ) ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null $locale
     * @param string|null $websiteId
     */
    public function set(string $name, $value, ?string $locale = null, ?string $websiteId = null): void
    {
        $locale = $this->resolveLocale($locale);
        $websiteId = $this->resolveWebsite($websiteId);

        $this->repository->updateValue(
            $name,
            $value,
            $locale,
            $websiteId,
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        $this->cache[$websiteId][$locale][$name] = $value;
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
}
