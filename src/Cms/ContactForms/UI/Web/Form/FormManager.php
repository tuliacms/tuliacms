<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForms\Application\Command\FormStorage;
use Tulia\Cms\ContactForms\Application\Model\Form as ApplicationForm;
use Tulia\Cms\ContactForms\Query\Model\Form as QueryForm;

/**
 * @author Adam Banaszkiewicz
 */
class FormManager
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormStorage
     */
    private $formStorage;

    /**
     * @var ApplicationForm
     */
    private $form;

    /**
     * @var QueryForm
     */
    private $sourceForm;

    /**
     * @param FormFactoryInterface $formFactory
     * @param FormStorage $formStorage
     * @param QueryForm $sourceForm
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormStorage $formStorage,
        QueryForm $sourceForm
    ) {
        $this->formFactory = $formFactory;
        $this->formStorage = $formStorage;
        $this->sourceForm = $sourceForm;
    }

    public function createForm(): FormInterface
    {
        $this->form = ApplicationForm::fromQueryModel($this->sourceForm);

        return $this->formFactory->create(Form::class, $this->form);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationForm $data */
        $data = $form->getData();

        $this->sourceForm->setId($data->getId());

        $this->formStorage->save($data);
    }
}
