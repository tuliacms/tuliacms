<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Widget\Application\Command\WidgetStorage;
use Tulia\Cms\Widget\Application\Model\Widget as ApplicationWidget;
use Tulia\Cms\Widget\Query\Model\Widget as QueryWidget;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetFormManagerFactory
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
     * @var WidgetStorage
     */
    private $widgetStorage;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param WidgetStorage $widgetStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        WidgetStorage $widgetStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->widgetStorage  = $widgetStorage;
    }

    public function create(WidgetInterface $widgetInstance, ?QueryWidget $widgetModel = null): WidgetFormManager
    {
        return new WidgetFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->widgetStorage,
            $widgetInstance,
            $widgetModel
        );
    }
}
