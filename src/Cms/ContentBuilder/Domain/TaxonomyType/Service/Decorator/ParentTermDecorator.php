<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\Decorator;

use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Model\TaxonomyType;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeDecoratorInterface;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParentTermDecorator implements TaxonomyTypeDecoratorInterface
{
    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeFlagRegistryInterface $flagRegistry)
    {
        $this->flagRegistry = $flagRegistry;
    }

    public function decorate(TaxonomyType $taxonomyType): void
    {
        if ($taxonomyType->isHierarchical() === false) {
            return;
        }

        $taxonomyType->addField(new Field([
            'name' => 'parent_id',
            'type' => 'taxonomy',
            'taxonomy' => $taxonomyType->getType(),
            'label' => 'parentTerm',
            'internal' => true,
            'builder_options' => function () use ($taxonomyType) {
                return [
                    'search_route_params' => [
                        'taxonomy_type' => $taxonomyType->getType(),
                    ],
                    'constraints' => [
                        new Callback(function ($value, ExecutionContextInterface $context) use ($taxonomyType) {
                            if (empty($value) === false && $value === $context->getRoot()->get('id')->getData()) {
                                $context->buildViolation('cannotAssignSelfTermParent')
                                    ->setTranslationDomain($taxonomyType->getTranslationDomain())
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
