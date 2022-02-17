<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

/**
 * @author Adam Banaszkiewicz
 */
class Crumb
{
    private string $code;
    private array $tags;
    private ?object $context;

    public function __construct(string $code, array $tags, ?object $context = null)
    {
        $this->code = $code;
        $this->tags = $tags;
        $this->context = $context;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getContext(): ?object
    {
        return $this->context;
    }
}
