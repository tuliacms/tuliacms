<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\Metadata\Domain\WriteModel\Model\Attribute;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeFormDescriptor
{
    protected ContentType $contentType;
    protected FormInterface $form;
    protected ?FormView $formView = null;

    public function __construct(ContentType $contentType, FormInterface $form)
    {
        $this->form = $form;
        $this->contentType = $contentType;
    }

    /**
     * @return Field[]
     */
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

    /**
     * @return Attribute[]
     */
    public function getData(): array
    {
        $rawData = $this->form->getData();

        $result['id'] = $rawData['id'];

        $result = $this->flattenFields($this->getFields(), $rawData);

        return $result;
    }

    public function isFormValid(): bool
    {
        return $this->form->isSubmitted() && $this->form->isValid();
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    /**
     * @param Field[] $fields
     */
    private function flattenFields(array $fields, array $rawData, string $uniquePrefix = '', string $prefix = ''): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field->isType('repeatable')) {
                $subfieldsGroups = array_values($rawData[$field->getCode()]);
                usort($subfieldsGroups, function (array $a, array $b) {
                    return $a['__order'] <=> $b['__order'];
                });

                foreach ($subfieldsGroups as $groupKey => $subfields) {
                    if ($uniquePrefix) {
                        $fieldCode = '[' . $field->getCode() . ']';
                    } else {
                        $fieldCode = $field->getCode();
                    }

                    $flatenedSubfields = $this->flattenFields(
                        $field->getChildren(),
                        $subfields,
                        sprintf('%s%s[%d]', $uniquePrefix, $fieldCode, $groupKey),
                        sprintf('%s%s.', $prefix, $field->getCode())
                    );

                    foreach ($flatenedSubfields as $code => $subfield) {
                        $result[$code] = $subfield;
                    }
                }
            } else {
                if ($uniquePrefix) {
                    $uri = $uniquePrefix . '[' . $field->getCode() . ']';
                } else {
                    $uri = $uniquePrefix . $field->getCode();
                }

                $result[$uri] = new Attribute(
                    $prefix . $field->getCode(),
                    $rawData[$field->getCode()],
                    $uri,
                    $field->getFlags(),
                    $field->isMultilingual(),
                    $field->hasNonscalarValue()
                );
            }
        }

        return $result;
    }
}
