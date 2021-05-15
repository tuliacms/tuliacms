<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\Factory;

use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermFactory implements TermFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;

    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator = $uuidGenerator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): Term
    {
        return Term::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'locale' => $this->currentWebsite->getLocale()->getCode(),
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }
}
