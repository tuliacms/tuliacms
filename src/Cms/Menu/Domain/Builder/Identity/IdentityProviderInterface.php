<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
interface IdentityProviderInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool;

    /**
     * @param string $identity
     *
     * @return IdentityInterface|null
     */
    public function provide(string $identity): ?IdentityInterface;
}
