<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

use Tulia\Cms\Menu\Infrastructure\Builder\Identity\Identity;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Identity\IdentityProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class IdentityProvider implements IdentityProviderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return strncmp($type, 'term:', 5) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $identity): ?IdentityInterface
    {
        return new Identity($this->router->generate('term_' . $identity), [ 'term-' . $identity ]);
    }
}