<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormWriteStorageInterface
{
    public function find(string $id, string $locale, string $defaultLocale): array;

    public function insert(array $form, string $defaultLocale): void;

    public function update(array $form, string $defaultLocale): void;

    public function delete(array $form): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
