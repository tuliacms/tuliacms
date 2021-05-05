<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Domain\Builder\Identity\Providers;

use Tulia\Cms\Menu\Domain\Builder\Identity\Identity;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityInterface;
use Tulia\Cms\Menu\Domain\Builder\Identity\IdentityProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageProvider implements IdentityProviderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $homepage;

    /**
     * @param RouterInterface $router
     * @param string $homepage
     */
    public function __construct(RouterInterface $router, string $homepage = 'homepage')
    {
        $this->router   = $router;
        $this->homepage = $homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $type): bool
    {
        return $type === 'simple:homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function provide(string $identity): ?IdentityInterface
    {
        return new Identity($this->router->generate($this->homepage));
    }
}
