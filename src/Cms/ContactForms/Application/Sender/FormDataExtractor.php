<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Sender;

use Tulia\Cms\ContactForms\Application\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormDataExtractor implements FormDataExtractorInterface
{
    /**
     * @var FieldsTypeRegistryInterface
     */
    private $fieldsTypes;

    /**
     * @param FieldsTypeRegistryInterface $fieldsTypes
     */
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
                if ($name !== $field['name']) {
                    continue;
                }

                $data[$name] = $this->fieldsTypes->get($field['type'])->prepareValueFromRequest(
                    $value,
                    $form->get($name)->getConfig()->getOptions()
                );
            }
        }

        return $data;
    }
}
