<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeaheadType extends AbstractType
{
    protected TermFinderInterface $termFinder;

    protected RouterInterface $router;

    public function __construct(TermFinderInterface $termFinder, RouterInterface $router)
    {
        $this->termFinder = $termFinder;
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
                $term = $this->termFinder->findOne(['id' => $criteria['value']], TermFinderScopeEnum::INTERNAL);

                return $term ? ['name' => $term->getTitle()] : null;
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
