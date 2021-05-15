<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager;

use DateTime;
use Exception;
use InvalidArgumentException;
use Tulia\Cms\Metadata\Domain\ReadModel\MagickMetadataTrait;

/**
 * @author Adam Banaszkiewicz
 */
class File implements FileInterface
{
    use MagickMetadataTrait;

    protected $id;
    protected $directory;
    protected $filename;
    protected $extension;
    protected $type;
    protected $mimetype;
    protected $size;
    protected $path;
    protected $createdAt;
    protected $updatedAt;

    /**
     * @param array $data
     * @return FileInterface
     * @throws Exception
     */
    public static function buildFromArray(array $data): FileInterface
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
     * {@inheritdoc}
     */
    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if (\array_key_exists($key, $data) === false) {
            $data[$key] = $default;
        } elseif ($data[$key] === null && $default === null) {
            // Do nothing, allow to null;
        } elseif ($data[$key] instanceof DateTime === false) {
            $data[$key] = new DateTime($data[$key]);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     */
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function setMimetype(string $mimetype): void
    {
        $this->mimetype = $mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
