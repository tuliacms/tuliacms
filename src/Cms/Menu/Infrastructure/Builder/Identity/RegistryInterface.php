<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return iterable
     */
    public function all(): iterable;

    /**
     * @param IdentityProviderInterface $provider
     */
    public function add(IdentityProviderInterface $provider): void;

    /**
     * @param string $type
     * @param string $identity
     *
     * @return IdentityInterface|null
     */
    public function provide(string $type, string $identity): ?IdentityInterface;
}
