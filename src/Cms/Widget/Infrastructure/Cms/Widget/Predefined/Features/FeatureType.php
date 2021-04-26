<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Features;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * @author Adam Banaszkiewicz
 */
class FeatureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', Type\TextType::class, [
                'label' => 'title',
            ])
            ->add('description', Type\TextareaType::class, [
                'label' => 'description',
            ])
            ->add('icon', Type\TextType::class, [
                'label' => 'icon',
                'attr' => [
                    'class' => 'filepicker-control',
                ],
            ])
            ->add('position', Type\HiddenType::class, [
                'attr' => [
                    'class' => 'position-control',
                ],
            ])
        ;
    }
}
