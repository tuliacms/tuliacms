<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Builder;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\ContactForms\Query\Model\Form as Model;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    public function build(Model $form, array $data = [], array $options = []): FormInterface
    {
        return $this->formFactory->createNamed('contact_form_' . $form->getId(), Form::class, $data, array_merge([
            'fields' => $form->getFields(),
            'action' => $this->router->generate('form.submit', [ 'id' => $form->getId() ]),
        ], $options));
    }
}
