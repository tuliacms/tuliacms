<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ModelTransformer;

use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;
use Tulia\Cms\ContactForm\Ports\Domain\FieldsParser\FieldsParserInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DomainModelTransformer
{
    private FieldsParserInterface $fieldsParser;

    public function __construct(FieldsParserInterface $fieldsParser)
    {
        $this->fieldsParser = $fieldsParser;
    }

    public function transform(Form $model): array
    {
        $result = [];
        $result['id'] = $model->getId();
        $result['receivers'] = $model->getReceivers();
        $result['sender_name'] = $model->getSenderName();
        $result['sender_email'] = $model->getSenderEmail();
        $result['reply_to'] = $model->getReplyTo();
        $result['name'] = $model->getName();
        $result['subject'] = $model->getSubject();
        $result['message_template'] = $model->getMessageTemplate();
        $result['fields_template'] = $model->getFieldsTemplate();

        $fields = [];

        foreach ($model->fields() as $field) {
            $fields[] = array_merge(
                ['name' => $field->getName()],
                ['type' => $field->getType()],
                ['type_alias' => $field->getTypeAlias()],
                $field->getOptions(),
            );
        }

        $result['fields'] = $fields;

        return $result;
    }

    /**
     * @throws MultipleFieldsInTemplateException
     * @throws InvalidFieldNameException
     */
    public function reverseTransform(array $source, Form $model): void
    {
        $model->setReceivers($source['receivers']);
        $model->setSenderName($source['sender_name']);
        $model->setSenderEmail($source['sender_email']);
        $model->setReplyTo($source['reply_to']);
        $model->setName($source['name']);
        $model->setSubject($source['subject']);
        $model->setMessageTemplate($source['message_template']);

        $fields = [];

        foreach ($source['fields'] as $field) {
            $name = $field['name'];
            $type = $field['alias'];

            unset($field['name'], $field['type']);

            $fields[] = [
                'name' => $name,
                'type' => $type,
                'options' => $field
            ];
        }

        $model->setFieldsTemplate(
            $fields,
            $source['fields_template'],
            $this->fieldsParser
        );
    }
}
