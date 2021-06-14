<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Frontend\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\ContactForms\Ports\UserInterface\Web\Frontend\FormBuilder\ContactFormBuilderInterface;
use Tulia\Cms\ContactForms\Query\Model\Form as Model;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormBuilder implements ContactFormBuilderInterface
{
    private FormFactoryInterface $formFactory;

    private RouterInterface $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function build(Model $form, array $data = [], array $options = []): FormInterface
    {
        $options = array_merge([
            'fields' => $form->getFields(),
            'action' => $this->router->generate('form.submit', [ 'id' => $form->getId() ]),
        ], $options);

        return $this->formFactory->createNamed(
            'contact_form_' . $form->getId(),
            ContactFormFramework::class,
            $data,
            $options
        );
    }
}
