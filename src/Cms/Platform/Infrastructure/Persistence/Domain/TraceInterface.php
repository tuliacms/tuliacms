<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Persistence\Domain;

/**
 * @author Adam Banaszkiewicz
 */
interface TraceInterface
{
    public function updateMainRow(array $data, bool $foreignLocale): void;
    public function insertMainRow(array $data): void;
    public function insertLangRow(array $data): void;
    public function updateLangRow(array $data): void;
    public function rootExists(string $id): bool;
    public function langExists(array $data): bool;
}
