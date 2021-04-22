<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Component\Routing\Exception\RoutingException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class NodeExtension extends AbstractExtension
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('node_path', function ($identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('node_url', function ($identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::ABSOLUTE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    private function generate($identity, array $parameters, int $type): string
    {
        // @todo generate links to fronted pages in backend panel
        return '';
        try {
            $context = clone $this->router->getContext();
            $context->setBackend(false);

            return $this->router->generate($this->getId($identity), $parameters, $type, $context);
        } catch (RoutingException $exception) {
            return '';
        }
    }

    private function getId($identity): string
    {
        if ($identity instanceof Node) {
            $id = $identity->getId();
        } elseif (is_string($identity)) {
            $id = $identity;
        } else {
            $id = '';
        }

        return 'node_' . $id;
    }
}
