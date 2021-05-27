<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetWriteStorageInterface
{
    public function find(string $id, string $locale, string $defaultLocale): array;

    public function insert(array $widget, string $defaultLocale): void;

    public function update(array $widget, string $defaultLocale): void;

    public function delete(array $widget): void;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}
