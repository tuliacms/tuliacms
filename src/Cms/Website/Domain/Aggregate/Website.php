<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Aggregate;

use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;
use Tulia\Cms\Website\Domain\Event;
use Tulia\Cms\Website\Domain\Exception;
use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AggregateRoot
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $backendPrefix;

    /**
     * @var LocaleCollection|Locale[]|array|iterable
     */
    private $locales;

    /**
     * @param string $id
     * @param string $name
     * @param string $backendPrefix
     * @param LocaleCollection $locales
     */
    public function __construct(AggregateId $id, string $name, string $backendPrefix, LocaleCollection $locales)
    {
        $this->id = $id;
        $this->name = $name;
        $this->backendPrefix = $backendPrefix;
        $this->locales = $locales ?? new LocaleCollection();

        $this->recordThat(new Event\WebsiteCreated($id, $name, $backendPrefix, $locales));
    }

    /**
     * @return AggregateId
     */
    public function getId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @param Locale $locale
     *
     * @throws Exception\DuplicatedLocaleAddedException
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    public function addLocale(Locale $locale): void
    {
        foreach ($this->locales as $el) {
            if ($locale->getCode() === $el->getCode()) {
                throw new Exception\DuplicatedLocaleAddedException(sprintf('Duplicated locale added, locale code %s.', $locale->getCode()));
            }
        }

        $this->validateLocale($locale);
        $this->locales->append($locale);
        $this->recordThat(Event\LocaleAdded::createFromAggregate($this->id, $locale));
    }

    /**
     * @param Locale $locale
     *
     * @throws Exception\LocaleNotExistsException
     */
    public function removeLocale(Locale $locale): void
    {
        foreach ($this->locales as $el) {
            if ($locale->getCode() === $el->getCode()) {
                $this->locales->remove($el);
                $this->recordThat(Event\LocaleRemoved::createFromAggregate($this->id, $locale));
                return;
            }
        }

        throw new Exception\LocaleNotExistsException(sprintf('Locale %s cannot be removed, because not exists in this website.', $locale->getCode()));
    }

    /**
     * @param Locale $locale
     *
     * @throws Exception\LocaleNotExistsException
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    public function updateLocale(Locale $locale): void
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
    }

    /**
     * @param Locale $one
     * @param Locale $two
     *
     * @return bool
     */
    private function areLocalesSame(Locale $one, Locale $two): bool
    {
        return $one->getCode() === $two->getCode()
            && $one->getDomain() === $two->getDomain()
            && $one->getLocalePrefix() === $two->getLocalePrefix()
            && $one->getPathPrefix() === $two->getPathPrefix()
            && $one->getSslMode() === $two->getSslMode()
            && $one->getIsDefault() === $two->getIsDefault()
        ;
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

    /**
     * @return LocaleCollection
     */
    public function getLocales(): LocaleCollection
    {
        $newCollection = new LocaleCollection();

        foreach ($this->locales as $locale) {
            $newCollection->append(clone $locale);
        }

        return $newCollection;
    }

    /**
     * @param string $name
     */
    public function rename(string $name): void
    {
        if ($this->name !== $name) {
            $this->name = $name;
            $this->recordThat(new Event\WebsiteRenamed($this->id, $name));
        }
    }

    /**
     * @param string $backendPrefix
     */
    public function changeBackendPrefix(string $backendPrefix): void
    {
        if ($this->backendPrefix !== $backendPrefix) {
            $this->backendPrefix = $backendPrefix;
            $this->recordThat(new Event\BackendPrefixChanged($this->id, $backendPrefix));
        }
    }

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
