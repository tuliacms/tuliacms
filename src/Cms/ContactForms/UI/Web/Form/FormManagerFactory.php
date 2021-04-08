<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\ContactForms\Application\Command\FormStorage;
use Tulia\Cms\ContactForms\Query\Model\Form as QueryForm;

/**
 * @author Adam Banaszkiewicz
 */
class FormManagerFactory
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
     * @param FormFactoryInterface $formFactory
     * @param FormStorage $formStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        FormStorage $formStorage
    ) {
        $this->formFactory  = $formFactory;
        $this->formStorage  = $formStorage;
    }

    public function create(?QueryForm $form = null): FormManager
    {
        return new FormManager(
            $this->formFactory,
            $this->formStorage,
            $form
        );
    }
}
