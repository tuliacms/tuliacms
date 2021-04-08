<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Tiles;

/**
 * @author Adam Banaszkiewicz
 */
interface TileInterface
{
    public function setTitle(string $title): void;
    public function getTitle(): string;
    public function setDescription(string $description): void;
    public function getDescription(): string;
    public function setIcon(string $icon): void;
    public function getIcon(): string;
    public function setLink(string $link): void;
    public function getLink(): string;
    public function setPriority(int $priority): void;
    public function getPriority(): int;
}
