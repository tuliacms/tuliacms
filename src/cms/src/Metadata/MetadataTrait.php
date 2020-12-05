<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata;

/**
 * @property MetadataInterface metadata
 *
 * @author Adam Banaszkiewicz
 */
trait MetadataTrait
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata(): MetadataInterface
    {
        $this->ensureMetadataSet();

        return $this->metadata;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getMeta(string $name, $default = null)
    {
        $this->ensureMetadataSet();

        return $this->metadata->get($name, $default);
    }

    /**
     * @param string $name
     * @param        $value
     */
    public function setMeta(string $name, $value): void
    {
        $this->ensureMetadataSet();

        $this->metadata->set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMeta(string $name): bool
    {
        $this->ensureMetadataSet();

        return $this->metadata->has($name);
    }

    protected function ensureMetadataSet(): void
    {
        if ($this->metadata === null) {
            throw new \RuntimeException('No Metadata was set in in this Object. Did You forget to setup this Object?');
        }
    }
}
