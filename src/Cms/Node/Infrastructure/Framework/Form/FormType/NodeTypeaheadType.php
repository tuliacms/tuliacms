<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeaheadType extends AbstractType
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.node.search.typeahead',
            'display_prop'  => 'title',
            'data_provider_single' => function (array $criteria): ?array {
                $node = $this->finderFactory->getInstance(ScopeEnum::INTERNAL)->find($criteria['value']);

                return $node ? ['title' => $node->getTitle()] : null;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TypeaheadType::class;
    }
}
