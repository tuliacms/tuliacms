<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query\Factory;

use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormFactory implements FormFactoryInterface
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param UuidGeneratorInterface $uuidGenerator
     * @param CurrentWebsiteInterface $currentWebsite
     */
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
    public function createNew(array $data = []): Form
    {
        return Form::buildFromArray(array_merge($data, [
            'id'         => $this->uuidGenerator->generate(),
            'locale'     => $this->currentWebsite->getLocale()->getCode(),
            'website_id' => $this->currentWebsite->getId(),
        ]));
    }
}
