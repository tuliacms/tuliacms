<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Query\Model\Website as QueryWebsite;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteFormManagerFactory
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
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param WebsiteStorage $websiteStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        WebsiteStorage $websiteStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->websiteStorage    = $websiteStorage;
    }

    public function create(?QueryWebsite $website = null): WebsiteFormManager
    {
        return new WebsiteFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->websiteStorage,
            $website
        );
    }
}
