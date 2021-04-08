<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeaheadType extends AbstractType
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param RouterInterface $router
     */
    public function __construct(FinderFactoryInterface $finderFactory, RouterInterface $router)
    {
        $this->finderFactory = $finderFactory;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.term.search.typeahead',
            'display_prop'  => 'name',
            'data_provider_single' => function (array $criteria): ?array {
                $term = $this->finderFactory->getInstance(ScopeEnum::INTERNAL)->find($criteria['value']);

                return $term ? ['name' => $term->getName()] : null;
            },
        ]);

        $resolver->setRequired(['taxonomy_type']);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TypeaheadType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $options['search_route_params'] = array_merge(
            $options['search_route_params'],
            [ 'taxonomy_type' => $options['taxonomy_type'] ]
        );

        $view->vars['typeahead_url'] = $this->router->generate(
            $options['search_route'],
            $options['search_route_params']
        );
    }
}
