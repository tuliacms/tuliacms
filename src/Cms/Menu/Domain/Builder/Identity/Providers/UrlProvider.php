<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity\Providers;

use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;

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
