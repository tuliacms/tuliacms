<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeaheadTypeBuilder implements FieldTypeBuilderInterface
{
    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $options['search_route_params'] = [
            'node_type' => $contentType->getCode(),
        ];
        $options['constraints'] += [
            new Callback(function ($value, ExecutionContextInterface $context) {
                if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                    $context->buildViolation('cannotAssignSelfNodeParent')
                        ->setTranslationDomain('node')
                        ->atPath('parent_id')
                        ->addViolation();
                }
            }),
        ];

        return $options;
    }
}
