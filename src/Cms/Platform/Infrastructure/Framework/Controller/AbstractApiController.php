<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\DependencyInjection\ContainerAwareInterface;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractApiController implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->container->get('parameters_bag')->getParameter($name);
    }

    /**
     * @param string|null $id
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     *
     * @return string|null
     */
    public function trans(?string $id, array $parameters = [], $domain = null, $locale = null): ?string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $name
     * @param array $params
     *
     * @return string
     */
    public function generateUrl(string $name, array $params = []): string
    {
        return $this->container->get(RouterInterface::class)->generate($name, $params, RouterInterface::TYPE_URL);
    }
}
