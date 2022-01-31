<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
interface IdentityProviderInterface
{
    public function supports(string $type): bool;

    public function provide(string $type, string $identity): ?IdentityInterface;
}
