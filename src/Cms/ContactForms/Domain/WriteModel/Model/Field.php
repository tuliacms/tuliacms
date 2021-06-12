<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\WriteModel\Model;

use Tulia\Cms\Platform\Domain\Model\IndentifyableEntityInterface;
use Tulia\Cms\Platform\Domain\ValueObject\EntityIdInterface;
use Tulia\Cms\Platform\Domain\ValueObject\SimpleEntityId;

/**
 * @author Adam Banaszkiewicz
 */
final class Field implements IndentifyableEntityInterface
{
    private SimpleEntityId $name;

    private string $type;

    private array $options;

    private function __construct(string $name, string $type, array $options = [])
    {
        $this->name = new SimpleEntityId($name);
        $this->type = $type;
        $this->options = $options;
    }

    public static function buildFromArray(array $data): self
    {
        return new self($data['name'], $data['type'], $data['options']);
    }

    public function getId(): EntityIdInterface
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name->getValue();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
