<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\Results;

/**
 * @author Adam Banaszkiewicz
 */
interface ResultsInterface
{
    public function toArray(): array;
    public function merge(ResultsInterface $results): void;
    public function add(Hit $hit): void;
    public function getHits(): array;
    public function setHits(array $hits): void;
    public function appendHits(array $hits): void;
    public function getLabel(): array;
    public function setLabel(array $label): void;
    public function getIcon(): string;
    public function setIcon(string $icon): void;
}
