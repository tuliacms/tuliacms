<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Persistence\Domain;

use InvalidArgumentException;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLocalizableStorage
{
    /**
     * @param array $data
     * @param string $defaultLocale
     * @throws InvalidArgumentException
     */
    public function insert(array $data, string $defaultLocale): void
    {
        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Missing "id" key in saved data.');
        }
        if (isset($data['locale']) === false) {
            throw new InvalidArgumentException('Missing "locale" key in saved data.');
        }

        $foreignLocale = $defaultLocale !== $data['locale'];

        $this->insertMainRow($data);

        if ($foreignLocale) {
            $this->insertLangRow($data);
        }
    }

    /**
     * @param array $data
     * @param string $defaultLocale
     * @throws InvalidArgumentException
     */
    public function update(array $data, string $defaultLocale): void
    {
        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('Missing "id" key in saved data.');
        }
        if (isset($data['locale']) === false) {
            throw new InvalidArgumentException('Missing "locale" key in saved data.');
        }

        $langRowExists = $this->langExists($data['id'], $data['locale']);
        $foreignLocale = $defaultLocale !== $data['locale'];

        $this->updateMainRow($data, $foreignLocale);

        if ($foreignLocale) {
            if ($langRowExists) {
                $this->updateLangRow($data);
            } else {
                $this->insertLangRow($data);
            }
        }
    }

    abstract protected function updateMainRow(array $data, bool $foreignLocale): void;
    abstract protected function insertMainRow(array $data): void;
    abstract protected function insertLangRow(array $data): void;
    abstract protected function updateLangRow(array $data): void;
    abstract protected function langExists(string $id, string $locale): bool;
}
