<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Routing;

use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Generator implements GeneratorInterface
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var FrontendRouteSuffixResolver
     */
    protected $frontendRouteSuffixResolver;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param FrontendRouteSuffixResolver $frontendRouteSuffixResolver
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver
    ) {
        $this->finderFactory = $finderFactory;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $params, RequestContextInterface $context): string
    {
        if (strncmp($name, 'node_', 5) !== 0) {
            return '';
        }

        $identity = substr($name, 5);

        $params = array_merge([
            '_locale' => $context->getContentLocale(),
        ], $params);

        try {
            /** @var Node $node */
            $node = $this->getNode($identity, $params['_locale']);
        } catch (\Exception $e) {
            return '';
        }

        if (!$node) {
            return '';
        }

        return $this->frontendRouteSuffixResolver->appendSuffix("/{$node->getSlug()}");
    }

    /**
     * @param $identity
     * @param string $locale
     *
     * @return Node|null
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getNode($identity, string $locale): ?Node
    {
        if ($identity instanceof Node) {
            if ($identity->getLocale() === $locale) {
                return $identity;
            }

            $identity = $identity->getId();
        }

        $finder = $this->finderFactory->getInstance(ScopeEnum::ROUTING_GENERATOR);
        $finder->setCriteria([
            'locale' => $locale,
            'id'     => $identity,
        ]);
        $finder->fetch();

        return $finder->getResult()->first();
    }
}
