<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel\Model;

use Tulia\Cms\Shared\Domain\WriteModel\Model\Entity\IndentifyableEntityInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\EntityIdInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\SimpleEntityId;

/**
 * @author Adam Banaszkiewicz
 */
final class Field implements IndentifyableEntityInterface
{
    private SimpleEntityId $name;

    private string $type;

    private string $typeAlias;

    private array $options;

    private function __construct(string $name, string $type, string $typeAlias, array $options = [])
    {
        $this->name = new SimpleEntityId($name);
        $this->type = $type;
        $this->typeAlias = $typeAlias;
        $this->options = $options;
    }

    public static function buildFromArray(array $data): self
    {
        return new self($data['name'], $data['type'], $data['type_alias'], $data['options']);
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

    public function getTypeAlias(): string
    {
        return $this->typeAlias;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
