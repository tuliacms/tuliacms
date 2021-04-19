<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\Factory;

use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermFactory implements TermFactoryInterface
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
    public function createNew(array $data = []): Term
    {
        $node = Term::buildFromArray(array_merge($data, [
            'id'            => $this->uuidGenerator->generate(),
            'locale'        => $this->currentWebsite->getLocale()->getCode(),
            'website_id'    => $this->currentWebsite->getId(),
        ]));

        $this->loader->load($node);

        return $node;
    }
}
