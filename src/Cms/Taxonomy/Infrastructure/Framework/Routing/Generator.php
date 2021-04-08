<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Generator implements GeneratorInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var FrontendRouteSuffixResolver
     */
    protected $frontendRouteSuffixResolver;

    /**
     * @param StorageInterface $storage
     * @param FrontendRouteSuffixResolver $frontendRouteSuffixResolver
     */
    public function __construct(
        StorageInterface $storage,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver
    ) {
        $this->storage = $storage;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $params, RequestContextInterface $context): string
    {
        if (strncmp($name, 'term_', 5) !== 0) {
            return '';
        }

        $path = $this->storage->find(substr($name, 5), $context->getContentLocale())['path'] ?? '';

        return $path ? $this->frontendRouteSuffixResolver->appendSuffix($path) : $path;
    }
}
