<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorator;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParentNodeDecorator implements NodeTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeFlagRegistryInterface $flagRegistry)
    {
        $this->flagRegistry = $flagRegistry;
    }

    public function decorate(NodeType $nodeType): void
    {
        if ($nodeType->isHierarchical() === false) {
            return;
        }

        $nodeType->addField(new Field([
            'name' => 'parent_id',
            'type' => 'node_select',
            'label' => 'parentNode',
            'internal' => true,
            'builder_options' => function () use ($nodeType) {
                return [
                    'search_route_params' => [
                        'node_type' => $nodeType->getType(),
                    ],
                    'constraints' => [
                        new Callback(function ($value, ExecutionContextInterface $context) use ($nodeType) {
                            if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                                $context->buildViolation('cannotAssignSelfNodeParent')
                                    ->setTranslationDomain($nodeType->getTranslationDomain())
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
