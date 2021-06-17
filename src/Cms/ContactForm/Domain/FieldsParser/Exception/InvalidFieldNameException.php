<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldsParser\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class InvalidFieldNameException extends \Exception
{
    private ?string $name = null;

    public static function fromName(string $name): self
    {
        $self = new self(sprintf('Field name must be alphanum with underscore, "%s" given.', $name));
        $self->name = $name;

        return $self;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
