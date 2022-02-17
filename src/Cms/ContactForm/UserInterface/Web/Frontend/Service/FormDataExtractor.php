<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Frontend\Service;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForm\Domain\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
class FormDataExtractor
{
    private FieldsTypeRegistryInterface $fieldsTypes;

    public function __construct(FieldsTypeRegistryInterface $fieldsTypes)
    {
        $this->fieldsTypes = $fieldsTypes;
    }

    public function extract(Form $model, FormInterface $form): array
    {
        $fields = $model->getFields();
        $data = $form->getData();

        foreach ($data as $name => $value) {
            foreach ($fields as $field) {
                if ($name !== $field->getName()) {
                    continue;
                }

                $data[$name] = $this->fieldsTypes->get($field->getTypeAlias())->prepareValueFromRequest(
                    $value,
                    $form->get($name)->getConfig()->getOptions()
                );
            }
        }

        return $data;
    }
}
