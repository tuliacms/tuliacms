<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Filter;

/**
 * @author Adam Banaszkiewicz
 */
class Filter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $comparison;

    /**
     * @var string
     */
    private $selector;

    public function __construct(string $name, $value, string $selector, string $comparison = ComparisonOperatorsEnum::HAS)
    {
        $this->name = $name;
        $this->value = $value;
        $this->comparison = $comparison;
        $this->selector = $selector;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getComparison(): string
    {
        return $this->comparison;
    }

    /**
     * @return string
     */
    public function getSelector(): string
    {
        return $this->selector;
    }
}
