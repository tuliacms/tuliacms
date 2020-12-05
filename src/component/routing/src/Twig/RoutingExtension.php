<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Twig;

use Tulia\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class RoutingExtension extends AbstractExtension
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
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', function (string $name, array $params = []) {
                return $this->router->generate($name, $params, RouterInterface::TYPE_PATH);
            }, [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('url', function (string $name, array $params = []) {
                return $this->router->generate($name, $params, RouterInterface::TYPE_URL);
            }, [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
