<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Query\Factory;

use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFactory implements NodeFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;

    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator  = $uuidGenerator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): Node
    {
        return Node::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'locale'     => $this->currentWebsite->getLocale()->getCode(),
            'node_type'  => 'page',
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }
}
