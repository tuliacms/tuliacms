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

    public function __construct(string $id, string $type = ChangesetTypeEnum::TEMPORARY, array $data = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function all(): array
    {
        return $this->data;
    }

    public function replace(array $data): void
    {
        $this->data = $data;
    }

    public function set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function remove(string $name): void
    {
        unset($this->data[$name]);
    }

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

    public function merge(ChangesetInterface $changeset): void
    {
        foreach ($changeset as $key => $val) {
            $this->set($key, $val);
        }
    }

    public function mergeArray(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->set($key, $val);
        }
    }

    public function cloneWithNewId(string $id): ChangesetInterface
    {
        $changeset = new self($id, $this->type, $this->data);
        $changeset->setTheme($this->theme);

        return $changeset;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
