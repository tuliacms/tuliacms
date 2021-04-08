<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Aggregate;

use Tulia\Cms\ContactForms\Domain\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForms\Domain\ValueObject\FieldName;

/**
 * @author Adam Banaszkiewicz
 */
final class Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @throws InvalidFieldNameException
     */
    public function __construct(string $name, string $type, array $options = [])
    {
        $this->name = new FieldName($name);
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
