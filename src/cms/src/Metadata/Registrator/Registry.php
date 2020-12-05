<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Registrator;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var array
     */
    protected $contentTypesFields = [];

    /**
     * @param array|iterable $registrators
     */
    public function __construct(iterable $registrators = [])
    {
        foreach ($registrators as $registrator) {
            $this->addRegistrator($registrator);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRegistrator(RegistratorInterface $registrator): void
    {
        $registrator->register($this);
    }

    /**
     * {@inheritdoc}
     */
    public function addContentFields(string $contentType, ContentFieldsInterface $fields): RegistryInterface
    {
        $this->contentTypesFields[$contentType] = $fields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentFields(string $contentType): ContentFieldsInterface
    {
        if (isset($this->contentTypesFields[$contentType]) === false) {
            $this->contentTypesFields[$contentType] = new ContentFields;
        }

        return $this->contentTypesFields[$contentType];
    }

    /**
     * {@inheritdoc}
     */
    public function hasContentFields(string $contentType): bool
    {
        return \array_key_exists($contentType, $this->contentTypesFields);
    }

    /**
     * {@inheritdoc}
     */
    public function getContentTypes(): array
    {
        return array_keys($this->contentTypesFields);
    }
}
