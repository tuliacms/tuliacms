<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Website\Exception\LocaleNotExistsException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteInterface
{
    public static function withNewLocale(WebsiteInterface $website, string $newLocale): WebsiteInterface;

    public function getId(): string;
    public function getName(): string;
    public function getBackendPrefix(): string;
    public function getLocales(): iterable;
    public function getLocale(): LocaleInterface;

    /**
     * @return LocaleInterface
     *
     * @throws LocaleNotExistsException
     */
    public function getDefaultLocale(): LocaleInterface;

    /**
     * @param string $code
     *
     * @return LocaleInterface
     *
     * @throws LocaleNotExistsException
     */
    public function getLocaleByCode(string $code): LocaleInterface;

    /**
     * @param null|string|LocaleInterface $locale
     *
     * @return string
     *
     * @throws LocaleNotExistsException
     */
    public function getAddress($locale = null): string;

    /**
     * @param null|string|LocaleInterface $locale
     *
     * @return string
     *
     * @throws LocaleNotExistsException
     */
    public function getBackendAddress($locale = null): string;
}
