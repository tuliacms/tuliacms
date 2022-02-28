<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

/**
 * @author Adam Banaszkiewicz
 */
interface ChangesetInterface extends \IteratorAggregate
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string|null
     */
    public function getTheme(): ?string;

    /**
     * @param string|null $theme
     */
    public function setTheme(?string $theme): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param array $data
     */
    public function replace(array $data): void;

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value): void;

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     */
    public function remove(string $name): void;

    /**
     * @return array
     */
    public function getAllMultilingual(): array;

    /**
     * @return array
     */
    public function getAllNotMultilingual(): array;

    /**
     * @param ChangesetInterface $changeset
     */
    public function merge(ChangesetInterface $changeset): void;

    /**
     * @param array $data
     */
    public function mergeArray(array $data): void;

    /**
     * @param string $id
     *
     * @return ChangesetInterface
     */
    public function cloneWithNewId(string $id): ChangesetInterface;
}
