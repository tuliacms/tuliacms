<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Features;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * @author Adam Banaszkiewicz
 */
class FeaturesForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('features', Type\CollectionType::class, [
                'entry_type' => FeatureType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }
}
