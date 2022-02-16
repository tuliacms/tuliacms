<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

use Tulia\Component\Theme\Enum\ChangesetTypeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class Changeset implements ChangesetInterface
{
    protected $id;
    protected $theme;
    protected $type = ChangesetTypeEnum::TEMPORARY;
    protected $fields = [];
    protected $data   = [];
    protected $fieldDefinitionDefaults = [
        'multilingual' => false,
    ];

    /**
     * @param string $id
     * @param array $data
     */
    public function __construct(string $id, array $data = [])
    {
        $this->id   = $id;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param null|string $theme
     */
    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldDefinitions(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinitions(): array
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldDefinition(string $field, array $definition): void
    {
        $this->fields[$field] = array_merge($this->fieldDefinitionDefaults, $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition(string $field): array
    {
        return $this->fields[$field] ?? $this->fieldDefinitionDefaults;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMultilingual(): array
    {
        $result = [];

        foreach ($this->fields as $id => $definition) {
            if ($definition['multilingual']) {
                $result[$id] = $this->get($id);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllNotMultilingual(): array
    {
        $result = [];

        foreach ($this->data as $id => $value) {
            if (isset($this->fields[$id]['multilingual']) === false || $this->fields[$id]['multilingual'] === false) {
                $result[$id] = $this->get($id);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(ChangesetInterface $changeset): void
    {
        foreach ($changeset as $key => $val) {
            $this->set($key, $val);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mergeArray(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->set($key, $val);
        }
    }

    /**
     * @param string $id
     *
     * @return ChangesetInterface
     */
    public function cloneWithNewId(string $id): ChangesetInterface
    {
        $changeset = new self($id, $this->data);
        $changeset->setFieldDefinitions($this->fields);
        $changeset->setTheme($this->theme);
        $changeset->setType($this->type);

        return $changeset;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
