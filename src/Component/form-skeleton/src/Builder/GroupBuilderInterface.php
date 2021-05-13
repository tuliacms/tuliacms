<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Builder;

/**
 * @author Adam Banaszkiewicz
 */
interface GroupBuilderInterface
{
    public function setOptions(array $options): void;

    public function getOption(string $name, $default = null): array;

    public function isSectionActive(string $id): bool;

    public function build(array $sections): string;

    public function supportsGroup(?string $group): bool;
}
