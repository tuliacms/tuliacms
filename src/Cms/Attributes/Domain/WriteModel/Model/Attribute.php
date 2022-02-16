<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\WriteModel\Model;

/**
 * @author Adam Banaszkiewicz
 */
class Attribute implements \Stringable
{
    private string $code;
    private $value;
    private string $uri;
    private array $flags;
    private bool $multilingual;
    private bool $hasNonscalarValue;

    /**
     * @param int|string|null|array $value
     */
    public function __construct(
        string $code,
        $value,
        string $uri,
        array $flags,
        bool $multilingual,
        bool $hasNonscalarValue
    ) {
        $this->code = $code;
        $this->value = $value;
        $this->uri = $uri;
        $this->flags = $flags;
        $this->multilingual = $multilingual;
        $this->hasNonscalarValue = $hasNonscalarValue;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function produceUriWithModificator(string $modificator): string
    {
        if ($this->uri[strlen($this->uri) - 1] === ']') {
            return substr($this->uri, 0, -1).':'.$modificator.']';
        } else {
            return $this->uri.':'.$modificator;
        }
    }

    public function produceCodeWithModificator(string $modificator): string
    {
        return $this->code.':'.$modificator;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return array|int|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string[]
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    public function is(string $flag): bool
    {
        return \in_array($flag, $this->flags, true);
    }

    public function isCompilable(): bool
    {
        return $this->is('compilable');
    }

    public function isMultilingual(): bool
    {
        return $this->multilingual;
    }

    public function hasNonscalarValue(): bool
    {
        return $this->hasNonscalarValue;
    }
}
