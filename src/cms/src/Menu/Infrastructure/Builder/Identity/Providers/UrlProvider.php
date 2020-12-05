<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Identity\Providers;

use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Identity;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\IdentityProviderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UrlProvider implements IdentityProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return $type === 'simple:url';
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $identity): ?IdentityInterface
    {
        return new Identity($identity);
    }
}
