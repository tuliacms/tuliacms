<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use DateTimeImmutable;

/**
 * @author Adam Banaszkiewicz
 */
interface FileInterface
{
    public static function buildFromArray(array $data): FileInterface;

    public function getId(): string;

    public function setId(string $id): void;

    public function getDirectory(): string;

    public function setDirectory(string $directory): void;

    public function getFilename(): string;

    public function setFilename(string $filename): void;

    public function getExtension(): string;

    public function setExtension(string $extension): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getMimetype(): ?string;

    public function setMimetype(?string $mimetype): void;

    public function getSize(): ?int;

    public function setSize(?int $size): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getCreatedAt(): DateTimeImmutable;

    public function setCreatedAt(DateTimeImmutable $createdAt): void;

    public function getUpdatedAt(): ?DateTimeImmutable;

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void;
}
