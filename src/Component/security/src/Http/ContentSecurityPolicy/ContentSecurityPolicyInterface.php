<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Http\ContentSecurityPolicy;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Adam Banaszkiewicz
 */
interface ContentSecurityPolicyInterface
{
    public function add($rule, $value): void;

    public function addNonce(string $rule, string $nonce): void;

    public function has($rule, $value = null): bool;

    public function get($rule): array;

    public function remove($rule, $value = null): void;

    public function compile(): string;

    public function appendToResponse(Response $response): void;

    public function removeFromResponse(Response $response): void;

    public function isActive(): bool;

    public function setActive(bool $active): void;

    public function createNonce(): string;
}
