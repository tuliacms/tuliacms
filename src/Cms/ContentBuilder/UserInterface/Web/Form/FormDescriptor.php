<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
class FormDescriptor
{
    private FormInterface $form;
    private NodeType $nodeType;
    private ?FormView $formView = null;

    public function __construct(NodeType $nodeType, FormInterface $form)
    {
        $this->form = $form;
        $this->nodeType = $nodeType;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getFormView(): FormView
    {
        if ($this->formView) {
            return $this->formView;
        }

        return $this->formView = $this->form->createView();
    }

    public function getNodeType(): NodeType
    {
        return $this->nodeType;
    }

    public function getData(): array
    {
        $rawData = $this->form->getData();

        $result['id'] = [
            'values' => $rawData['id'],
            'multiple' => false,
        ];

        foreach ($this->getNodeType()->getFields() as $field) {
            $result[$field->getName()] = [
                'values' => $rawData[$field->getName()],
                'multiple' => $field->isMultiple(),
            ];
        }

        return $result;
    }

    public function isFormValid(): bool
    {
        return $this->form->isSubmitted() && $this->form->isValid();
    }
}
