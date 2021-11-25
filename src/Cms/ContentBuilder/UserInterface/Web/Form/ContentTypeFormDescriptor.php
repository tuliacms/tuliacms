<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\AbstractContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeFormDescriptor
{
    protected AbstractContentType $contentType;
    protected FormInterface $form;
    protected ?FormView $formView = null;

    public function __construct(AbstractContentType $contentType, FormInterface $form)
    {
        $this->form = $form;
        $this->contentType = $contentType;
    }

    protected function getFields(): array
    {
        return $this->contentType->getFields();
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

    public function getData(): array
    {
        $rawData = $this->form->getData();

        $result['id'] = $rawData['id'];

        foreach ($this->getFields() as $field) {
            $result[$field->getName()] = $rawData[$field->getName()];
        }

        return $result;
    }

    public function isFormValid(): bool
    {
        return $this->form->isSubmitted() && $this->form->isValid();
    }

    public function getContentType(): AbstractContentType
    {
        return $this->contentType;
    }
}
