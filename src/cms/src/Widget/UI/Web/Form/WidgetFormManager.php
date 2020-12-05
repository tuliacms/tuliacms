<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Widget\Application\Command\WidgetStorage;
use Tulia\Cms\Widget\Application\Model\Widget as ApplicationWidget;
use Tulia\Cms\Widget\Query\Model\Widget as QueryWidget;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;
use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetFormManager
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
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var ApplicationWidget
     */
    private $widgetInstance;

    /**
     * @var ConfigurationInterface
     */
    private $widgetConfiguration;

    /**
     * @var QueryWidget
     */
    private $widgetModel;

    /**
     * @var QueryWidget
     */
    private $widget;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param WidgetStorage $widgetStorage
     * @param QueryWidget $widgetModel
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        WidgetStorage $widgetStorage,
        WidgetInterface $widgetInstance,
        QueryWidget $widgetModel
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->widgetStorage  = $widgetStorage;
        $this->widgetInstance = $widgetInstance;
        $this->widgetModel    = $widgetModel;
    }

    public function createForm(): FormInterface
    {
        $this->widget = ApplicationWidget::fromQueryModel($this->widgetModel);

        $this->widgetConfiguration = $this->widget->getWidgetConfiguration();
        $configs = $this->widgetConfiguration->all();
        $this->widgetInstance->configure($this->widgetConfiguration);
        $this->widgetConfiguration->merge($configs);

        $this->widget->setWidgetConfiguration($this->widgetConfiguration);

        return $this->form = $this->getManager()->createForm(WidgetForm::class, $this->widget, [
            'widget_form' => $this->widgetInstance->getForm($this->widgetConfiguration),
        ]);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationWidget $data */
        $data = $form->getData();

        $this->widgetModel->setId($data->getId());
        $this->widgetInstance->saveAction($form, $data->getWidgetConfiguration());

        $this->widgetStorage->save($data);
    }

    public function getManager(): ManagerInterface
    {
        if ($this->manager) {
            return $this->manager;
        }

        return $this->manager = $this->managerFactory->getInstanceFor($this->widget, ScopeEnum::BACKEND_EDIT);
    }

    public function getWidgetView(): ?ViewInterface
    {
        $widgetView = $this->widgetInstance->getView($this->widgetConfiguration);

        if ($widgetView) {
            $widgetView->addData([
                'config' => $this->widgetConfiguration,
                'form'   => $this->form,
            ]);
        }

        return $widgetView;
    }
}
