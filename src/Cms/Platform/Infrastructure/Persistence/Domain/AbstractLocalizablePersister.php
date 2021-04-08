<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Persistence\Domain;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLocalizablePersister
{
    /**
     * @param array $data
     * @param string $defaultLocale
     *
     * @throws \InvalidArgumentException
     */
    public function save(array $data, string $defaultLocale): void
    {
        if (isset($data['id']) === false) {
            throw new \InvalidArgumentException('Missing "id" key in saved data.');
        }
        if (isset($data['locale']) === false) {
            throw new \InvalidArgumentException('Missing "locale" key in saved data.');
        }

        $mainRowExists = $this->rootExists($data['id']);
        $langRowExists = $this->langExists($data['id'], $data['locale']);

        $foreignLocale = $defaultLocale !== $data['locale'];

        if ($mainRowExists) {
            $this->updateMainRow($data, $foreignLocale);

            if ($foreignLocale) {
                if ($langRowExists) {
                    $this->updateLangRow($data);
                } else {
                    $this->insertLangRow($data);
                }
            }
        } else {
            $this->insertMainRow($data);

            if ($foreignLocale) {
                $this->insertLangRow($data);
            }
        }
    }

    abstract protected function updateMainRow(array $data, bool $foreignLocale): void;

    abstract protected function insertMainRow(array $data): void;

    abstract protected function insertLangRow(array $data): void;

    abstract protected function updateLangRow(array $data): void;

    abstract protected function rootExists(string $id): bool;

    abstract protected function langExists(string $id, string $locale): bool;
}
