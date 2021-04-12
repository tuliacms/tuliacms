<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Component\Routing\Exception\RoutingException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyExtension extends AbstractExtension
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
    public function getFunctions()
    {
        return [
            new TwigFunction('term_path', function ($identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::TYPE_PATH);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('term_url', function ($identity, array $parameters = []) {
                return $this->generate($identity, $parameters, RouterInterface::TYPE_URL);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    private function generate($identity, array $parameters, int $type): string
    {
        try {
            $context = clone $this->router->getRequestContext();
            $context->setBackend(false);

            return $this->router->generate($this->getId($identity), $parameters, $type, $context);
        } catch (RoutingException $exception) {
            return '';
        }
    }

    private function getId($identity): string
    {
        if ($identity instanceof Term) {
            $id = $identity->getId();
        } elseif (is_string($identity)) {
            $id = $identity;
        } else {
            $id = '';
        }

        return 'term_' . $id;
    }
}
