<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Application\Model\Website as ApplicationWebsite;
use Tulia\Cms\Website\Query\Model\Website as QueryWebsite;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteFormManager
{
    /**
     * @var ManagerFactoryInterface
     */
    private $managerFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var WebsiteStorage
     */
    private $websiteStorage;

    /**
     * @var ApplicationWebsite
     */
    private $website;

    /**
     * @var QueryWebsite
     */
    private $sourceWebsite;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param WebsiteStorage $websiteStorage
     * @param QueryWebsite $sourceWebsite
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        WebsiteStorage $websiteStorage,
        QueryWebsite $sourceWebsite
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->websiteStorage = $websiteStorage;
        $this->sourceWebsite  = $sourceWebsite;
    }

    public function createForm(): FormInterface
    {
        $this->website = ApplicationWebsite::fromQueryModel($this->sourceWebsite);

        return $this->formFactory->create(WebsiteForm::class, $this->website);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationWebsite $data */
        $data = $form->getData();

        $this->sourceWebsite->setId($data->getId());

        $this->websiteStorage->save($data);
    }
}
