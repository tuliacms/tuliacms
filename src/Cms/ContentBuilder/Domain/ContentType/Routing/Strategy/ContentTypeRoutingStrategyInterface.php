<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentTypeRoutingStrategyInterface
{
    public function generate(string $id, array $parameters = []): string;

    public function match(string $pathinfo, array $parameters = []): array;

    public function supports(string $contentType): bool;

    public function getId(): string;
}
