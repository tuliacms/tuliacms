<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Node\Infrastructure\Framework\Form\FormType\NodeTypeaheadType;

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
        $builder->add('node_search_' . $options['node_type']->getType(), NodeTypeaheadType::class, [
            'label' => 'node',
            'translation_domain' => $options['node_type']->getTranslationDomain(),
            'search_route_params' => [
                'node_type' => $options['node_type']->getType(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['node_type']);
    }
}
