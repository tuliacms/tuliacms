<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\Factory;

use Tulia\Cms\Widget\Query\Model\Widget;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetFactory implements WidgetFactoryInterface
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
    public function createNew(array $data = []): Widget
    {
        $node = Widget::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'locale'     => $this->currentWebsite->getLocale()->getCode(),
            'website_id' => $this->currentWebsite->getId(),
        ]));

        return $node;
    }
}
