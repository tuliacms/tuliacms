<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldsParser\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class InvalidFieldNameException extends \Exception
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @param string $name
     *
     * @return static
     */
    public static function fromName(string $name): self
    {
        $self = new self(sprintf('Field name must be alphanum with underscore, "%s" given.', $name));
        $self->name = $name;

        return $self;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
