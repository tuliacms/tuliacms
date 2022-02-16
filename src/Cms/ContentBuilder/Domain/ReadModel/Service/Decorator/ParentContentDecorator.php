<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\Decorator;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParentContentDecorator implements ContentTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeFlagRegistryInterface $flagRegistry)
    {
        $this->flagRegistry = $flagRegistry;
    }

    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        if ($contentType->isHierarchical() === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'parent_id',
            'type' => 'node_select',
            'name' => 'parentNode',
            'is_internal' => true,
            'builder_options' => function () use ($contentType) {
                return [
                    'search_route_params' => [
                        'node_type' => $contentType->getCode(),
                    ],
                    'constraints' => [
                        new Callback(function ($value, ExecutionContextInterface $context) use ($contentType) {
                            if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                                $context->buildViolation('cannotAssignSelfNodeParent')
                                    ->setTranslationDomain('node')
                                    ->atPath('parent_id')
                                    ->addViolation();
                            }
                        }),
                    ]
                ];
            }
        ]));
    }
}
