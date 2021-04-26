<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadType extends AbstractType
{
    protected RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            /**
             * Callback that will be used to search entities elements.
             * Can be used to add more specific search criteria.
             */
            'data_provider_single' => null,
            /**
             * Search route. Used by typeahead to make AJAX request and search for
             * elements matched to search by this field.
             */
            'search_route' => null,
            /**
             * Additional parameters for 'search_route'.
             */
            'search_route_params' => [],
            /**
             * This value will be used, when control value will be empty.
             * Ie. when creating element using this control type,
             * and default value will not be provided. Must be ID of entity.
             */
            'empty_data' => null,
            /**
             * Allows search multiple elements, and every next adds as tags, when user
             * can remove from list.
             */
            'multiple' => false,
            'compound' => false,
            'debug' => false,
        ]);

        $resolver->setRequired([ 'search_route', 'data_provider_single', 'display_prop' ]);

        $resolver->setAllowedTypes('data_provider_single', [ 'null', 'callable' ]);
        $resolver->setAllowedTypes('search_route', 'string');
        $resolver->setAllowedTypes('search_route_params', 'array');
    }

    /**
     * {@inheritdoc}
     *
     * @throws RouteNotFoundException
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($view->vars['typeahead_url']) === false) {
            $view->vars['typeahead_url'] = $this->router->generate(
                $options['search_route'],
                $options['search_route_params']
            );
        }

        $view->vars['debug']         = $options['debug'];
        $view->vars['multiple']      = $options['multiple'];
        $view->vars['display_prop']  = $options['display_prop'];
        $view->vars['display_value'] = null;

        /**
         * When value is empty, and field has provided empty_data value, we use this
         * provided value as field value.
         */
        if (! $view->vars['value'] && $options['empty_data']) {
            $view->vars['value'] = $options['empty_data'];
        }

        /**
         * Is value is provided, we use it as ID of entity, and find this entity
         * using repository and fill display_value to show it in HTML control.
         */
        if ($view->vars['value']) {
            if (\is_callable($options['data_provider_single'])) {
                $selected = \call_user_func($options['data_provider_single'], ['value' => $view->vars['value']]);

                if ($selected) {
                    $view->vars['display_value'] = $selected[$options['display_prop']];
                }
            } else {
                $view->vars['display_value'] = $view->vars['value'];
            }
        }
    }
}
