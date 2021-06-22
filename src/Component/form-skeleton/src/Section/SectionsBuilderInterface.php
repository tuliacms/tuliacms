<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Section;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionsBuilderInterface
{
    public function add(string $id, array $data): SectionsBuilderInterface;

    public function get(string $id): array;

    public function all(?string $group = null): array;
}
