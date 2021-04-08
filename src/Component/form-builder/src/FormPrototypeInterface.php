<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

/**
 * @author Adam Banaszkiewicz
 */
interface FormPrototypeInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param $data
     */
    public function setData($data): void;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @param array $options
     */
    public function setOptions(array $options): void;
}
