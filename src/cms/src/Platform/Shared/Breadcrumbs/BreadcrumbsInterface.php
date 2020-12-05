<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Breadcrumbs;

/**
 * @author Adam Banaszkiewicz
 */
interface BreadcrumbsInterface extends \IteratorAggregate
{
    public function all(): array;

    public function count(): int;

    public function push($href, $label): void;

    public function pop(): array;

    public function replace(array $crumbs): void;

    public function unshift($href, $label): void;

    public function shift(): array;

    public function render(): string;
}
