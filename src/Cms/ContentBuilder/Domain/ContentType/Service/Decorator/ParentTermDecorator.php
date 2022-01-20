<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Decorator;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParentTermDecorator implements ContentTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeFlagRegistryInterface $flagRegistry)
    {
        $this->flagRegistry = $flagRegistry;
    }

    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('taxonomy') === false) {
            return;
        }

        if ($contentType->isHierarchical() === false) {
            return;
        }

        $contentType->addField(new Field([
            'name' => 'parent_id',
            'type' => 'taxonomy',
            'taxonomy' => $contentType->getCode(),
            'label' => 'parentTerm',
            'internal' => true,
            'builder_options' => function () use ($contentType) {
                return [
                    'search_route_params' => [
                        'taxonomy_type' => $contentType->getCode(),
                    ],
                    'constraints' => [
                        new Callback(function ($value, ExecutionContextInterface $context) use ($contentType) {
                            if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                                $context->buildViolation('cannotAssignSelfTermParent')
                                    ->setTranslationDomain('taxonomy')
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
