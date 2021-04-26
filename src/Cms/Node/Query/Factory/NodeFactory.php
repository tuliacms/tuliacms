<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query\Factory;

use Tulia\Cms\Node\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFactory implements NodeFactoryInterface
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param UuidGeneratorInterface $uuidGenerator
     * @param Loader $loader
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        Loader $loader,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator  = $uuidGenerator;
        $this->loader         = $loader;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): Node
    {
        $node = Node::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'locale'     => $this->currentWebsite->getLocale()->getCode(),
            'node_type'  => 'page',
            'website_id' => $this->currentWebsite->getId(),
        ]));

        $this->loader->load($node);

        return $node;
    }
}
