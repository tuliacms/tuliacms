<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Domain\ReadModel\Model\Website as QueryWebsite;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteFormManagerFactory
{
    private ManagerFactoryInterface $managerFactory;
    private FormFactoryInterface $formFactory;
    private WebsiteStorage $websiteStorage;

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
