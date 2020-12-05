<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class FormPrototype implements FormPrototypeInterface
{
    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $type
     * @param mixed $data
     * @param array $options
     */
    public function __construct(string $type, $data, array $options = [])
    {
        $this->type    = $type;
        $this->data    = $data;
        $this->options = $options;
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
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }
}
