<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionsBuilderInterface
{
    public function add(string $id, array $data): SectionsBuilderInterface;

    public function all(?string $group = null): array;
}
