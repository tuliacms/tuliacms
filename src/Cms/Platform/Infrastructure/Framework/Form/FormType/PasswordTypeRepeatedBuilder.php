<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordTypeRepeatedBuilder implements FieldTypeBuilderInterface
{
    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $options['constraints'][] = new Callback([$this, 'validateFieldsValuesSame'], null, [
            'referenced_field' => $field->getConfig('referenced_field')
        ]);

        return $options;
    }

    /**
     * @param string|null $value
     */
    public function validateFieldsValuesSame($value, ExecutionContext $context, array $payload = []): void
    {
        $root = $context->getRoot();

        if ($root->has($payload['referenced_field']) === false) {
            return;
        }

        /** @var FormInterface $referencedField */
        $referencedField = $root->get($payload['referenced_field']);

        if ($referencedField->getData() !== $value) {
            $context->buildViolation('Value of this field must be same as of %referenced_field%.')
                ->setParameter('%referenced_field%', $referencedField->getConfig()->getOption('label'))
                ->addViolation();
        }
    }
}
