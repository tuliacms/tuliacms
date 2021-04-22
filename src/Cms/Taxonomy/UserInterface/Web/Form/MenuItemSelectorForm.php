<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemSelectorForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('term_search_' . $options['taxonomy_type']->getType(), TaxonomyTypeaheadType::class, [
            'label' => 'term',
            'translation_domain' => $options['taxonomy_type']->getTranslationDomain(),
            'taxonomy_type' => $options['taxonomy_type']->getType(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['taxonomy_type']);
    }
}
