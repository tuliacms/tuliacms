<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ReadModel\Model;

use DateTime;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;

/**
 * @author Adam Banaszkiewicz
 */
class File
{
    use MagickMetadataTrait;

    protected string $id;

    protected string $directory;

    protected string $filename;

    protected string $extension;

    protected string $type;

    protected ?string $mimetype = null;

    protected ?int $size = null;

    protected string $path;

    protected DateTimeImmutable $createdAt;

    protected ?DateTimeImmutable $updatedAt = null;

    public static function buildFromArray(array $data): self
    {
        $file = new self();

        if (isset($data['id']) === false) {
            throw new InvalidArgumentException('File ID must be provided.');
        }

        $data = static::setDatetime($data, 'created_at', new DateTime());
        $data = static::setDatetime($data, 'updated_at');

        $file->setId($data['id']);
        $file->setDirectory($data['directory'] ?? '');
        $file->setFilename($data['filename'] ?? '');
        $file->setExtension($data['extension'] ?? '');
        $file->setType($data['type'] ?? '');
        $file->setMimetype($data['mimetype'] ?? '');
        $file->setSize((int) ($data['size'] ?? 0));
        $file->setPath($data['path'] ?? '');
        $file->setCreatedAt($data['created_at']);
        $file->setUpdatedAt($data['updated_at']);
        $file->replaceMetadata($data['metadata'] ?? []);

        return $file;
    }

    /**
     * @throws Exception
     */
    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if (\array_key_exists($key, $data) === false) {
            $data[$key] = $default;
        } elseif ($data[$key] === null && $default === null) {
            // Do nothing, allow to null;
        } elseif ($data[$key] instanceof DateTimeImmutable === false) {
            $data[$key] = new DateTimeImmutable($data[$key]);
        }

        return $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(?string $mimetype): void
    {
        $this->mimetype = $mimetype;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
