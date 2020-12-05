<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Provider;

use Tulia\Cms\SearchAnything\Results\ResultsInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractProvider implements ProviderInterface
{
    abstract public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface;
    abstract public function getId(): string;
    public function getLabel(): array
    {
        return [''];
    }

    public function getIcon(): string
    {
        return 'fas fa-circle';
    }
}
