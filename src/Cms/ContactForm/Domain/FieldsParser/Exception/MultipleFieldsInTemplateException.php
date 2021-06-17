<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldsParser\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class MultipleFieldsInTemplateException extends \Exception
{
    private string $name;

    public static function fromName(string $name): self
    {
        $e = new self(sprintf('Detected multiple occurencies of %s in Fields template.', $name));
        $e->name = $name;

        return $e;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
